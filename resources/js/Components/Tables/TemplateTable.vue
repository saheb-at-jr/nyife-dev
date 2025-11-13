<!-- <script setup>
import axios from 'axios';
import AlertModal from '@/Components/AlertModal.vue';
import { Link } from "@inertiajs/vue3";
import { useAlertModal } from '@/Composables/useAlertModal';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import Table from '@/Components/Table.vue';
import TableHeader from '@/Components/TableHeader.vue';
import TableHeaderRow from '@/Components/TableHeaderRow.vue';
import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
import TableBody from '@/Components/TableBody.vue';
import TableBodyRow from '@/Components/TableBodyRow.vue';
import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const deleteAction = async (key) => {
    try {
        const response = await axios.delete(`/templates/${key}`);

        if (response.status === 200 && response.data.success) {
            const idx = props.rows.data.findIndex((i) => i.uuid === key);
            props.rows.data.splice(idx, 1);
        }

        toast(response.data.message, {
            autoClose: 3000,
        });
    } catch (error) {
    }
};

const emit = defineEmits(['delete']);

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const capitalizeFirstLetter = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

const findBodyText = (item) => {
    const metadataArray = JSON.parse(item);
    const bodyArray = metadataArray.components.find(element => element.type === "BODY");

    if (bodyArray) {
        return bodyArray.text;
    }

    return 'N/A';
}
</script>
<template>
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'">{{ $t('Name') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Category') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Preview') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Status') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Last updated') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                <TableBodyRowItem :position="'first'">{{ item.name }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ capitalizeFirstLetter(item.category) }}
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <div class="py-1 px-2 bg-gray-50 rounded-[5px] border border-dashed w-[20em] truncate text-xs">
                        {{ findBodyText(item.metadata) }}
                    </div>
                </TableBodyRowItem>
                <TableBodyRowItem>
                    <span v-if="item.status == 'APPROVED'"
                        class="py-1 rounded-[5px] text-xs px-3 bg-[#ebfdf4] text-[#38733f]">{{
                            $t(capitalizeFirstLetter(item.status)) }}</span>
                    <span v-else-if="item.status == 'REJECTED'"
                        class="py-1 rounded-[5px] text-xs px-3 bg-[#fae6e6] text-[darkred]">{{
                            $t(capitalizeFirstLetter(item.status)) }}</span>
                    <span v-else class="py-1 rounded-[5px] text-xs px-3 bg-[#ddebf7] text-slate-700">{{
                        $t(capitalizeFirstLetter(item.status)) }}</span>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ item.updated_at }}</TableBodyRowItem>
                <TableBodyRowItem :position="'last'">
                    <Dropdown :align="'right'" class="mt-2">
                        <button
                            class="inline-flex w-full justify-center rounded-md text-sm font-medium text-black hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                            <span class="hover:bg-[#F6F7F9] hover:rounded-full w-[fit-content] p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z" />
                                </svg>
                            </span>
                        </button>
                        <template #items>
                            <DropdownItemGroup>
                                <DropdownItem
                                    v-if="item.status == 'APPROVED' || item.status == 'REJECTED' || item.status == 'PAUSED'"
                                    :href="'/templates/' + item.uuid">{{ $t('edit') }}</DropdownItem>
                                <DropdownItem as="button" @click="openAlert(item.uuid)">{{ $t('Delete') }}
                                </DropdownItem>
                            </DropdownItemGroup>
                        </template>
                    </Dropdown>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>
    <div v-if="rows.data.length == 0" class="bg-white rounded-xl">
        <div class="p-4 py-8">
            <div class="flex justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 32 32">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="M12 15h8m-8 4h8m8 5V11c0-1.105-.892-2-1.997-2H17c-2 0-2-3-5-3H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h20a2 2 0 0 0 2-2Z">
                    </path>
                </svg>
            </div>
            <h3 class="text-center text-lg font-medium mb-4">{{ $t('You don\'t have any templates') }}</h3>
            <div class="flex justify-center">
                <Link href="/templates/create"
                    class="rounded-md px-3 py-2 text-sm hover:shadow-md text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 bg-primary"
                    :disabled="isLoading">
                <span v-if="!isLoading">{{ $t('Create template') }}</span>
                </Link>
            </div>
        </div>
    </div>

    <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')" />
</template> -->


<!-- ========================================== NEW UI CODE ==================================== -->


<script setup>
import axios from 'axios';
import AlertModal from '@/Components/AlertModal.vue';
import { Link } from "@inertiajs/vue3";
import { useAlertModal } from '@/Composables/useAlertModal';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import Table from '@/Components/Table.vue';
import TableHeader from '@/Components/TableHeader.vue';
import TableHeaderRow from '@/Components/TableHeaderRow.vue';
import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
import TableBody from '@/Components/TableBody.vue';
import TableBodyRow from '@/Components/TableBodyRow.vue';
import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import { FileText, Edit, Trash2, CheckCircle2, XCircle, Clock, Eye, Calendar, Tag } from 'lucide-vue-next';

const props = defineProps({
    viewMode: {
        type: String,
        default: 'list'
    },
    rows: {
        type: Object,
        required: true,
    },
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

const deleteAction = async (key) => {
    try {
        const response = await axios.delete(`/templates/${key}`);

        if (response.status === 200 && response.data.success) {
            const idx = props.rows.data.findIndex((i) => i.uuid === key);
            props.rows.data.splice(idx, 1);
        }

        toast(response.data.message, {
            autoClose: 3000,
        });
    } catch (error) {
    }
};

const emit = defineEmits(['delete']);

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const capitalizeFirstLetter = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

const findBodyText = (item) => {
    const metadataArray = JSON.parse(item);
    const bodyArray = metadataArray.components.find(element => element.type === "BODY");

    if (bodyArray) {
        return bodyArray.text;
    }

    return 'N/A';
}

const getStatusConfig = (status) => {
    const configs = {
        'APPROVED': {
            bg: 'bg-gradient-to-r from-emerald-500 to-teal-500',
            text: 'text-white',
            icon: CheckCircle2
        },
        'REJECTED': {
            bg: 'bg-gradient-to-r from-red-500 to-rose-500',
            text: 'text-white',
            icon: XCircle
        },
        'PAUSED': {
            bg: 'bg-gradient-to-r from-amber-500 to-orange-500',
            text: 'text-white',
            icon: Clock
        },
        'default': {
            bg: 'bg-gradient-to-r from-slate-500 to-gray-500',
            text: 'text-white',
            icon: Clock
        }
    };
    return configs[status] || configs.default;
}

const getCategoryColor = (category) => {
    const colors = {
        'marketing': 'bg-purple-100 text-purple-700',
        'utility': 'bg-blue-100 text-blue-700',
        'authentication': 'bg-green-100 text-green-700',
    };
    return colors[category?.toLowerCase()] || 'bg-gray-100 text-gray-700';
}

</script>

<template>
    <div class="min-h-screen">
        <!-- Grid View -->
        <div v-if="props.viewMode === 'grid' && rows.data.length > 0"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="(item, index) in rows.data" :key="index"
                class="group bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border-2 border-transparent hover:border-primary/20 transform hover:-translate-y-2">

                <!-- Card Header with Status -->
                <div :class="['p-6 pb-4', getStatusConfig(item.status).bg]">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                <span
                                    :class="['text-xs font-bold uppercase tracking-wider', getStatusConfig(item.status).text]">
                                    {{ capitalizeFirstLetter(item.status) }}
                                </span>
                            </div>
                            <h3 class="text-white font-bold text-xl line-clamp-2 group-hover:underline">
                                {{ item.name }}
                            </h3>
                        </div>

                        <Dropdown :align="'right'">
                            <button class="p-2 hover:bg-white/20 rounded-xl transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-white">
                                    <circle cx="12" cy="12" r="1" />
                                    <circle cx="12" cy="5" r="1" />
                                    <circle cx="12" cy="19" r="1" />
                                </svg>
                            </button>
                            <template #items>
                                <DropdownItemGroup>
                                    <DropdownItem
                                        v-if="item.status == 'APPROVED' || item.status == 'REJECTED' || item.status == 'PAUSED'"
                                        :href="'/templates/' + item.uuid">
                                        {{ $t('edit') }}
                                    </DropdownItem>
                                    <DropdownItem as="button" @click="openAlert(item.uuid)">
                                        {{ $t('Delete') }}
                                    </DropdownItem>
                                </DropdownItemGroup>
                            </template>
                        </Dropdown>
                    </div>

                    <div class="flex items-center gap-2 text-white/90 text-sm">
                        <Tag class="w-4 h-4" />
                        <span class="font-medium">{{ capitalizeFirstLetter(item.category) }}</span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Preview -->
                        <div
                            class="bg-gradient-to-r from-gray-50 to-orange-50 rounded-2xl p-4 border-2 border-primary/10">
                            <div class="flex items-start gap-2 mb-2">
                                <Eye class="w-4 h-4 text-primary flex-shrink-0 mt-0.5" />
                                <span class="text-sm font-semibold text-gray-700">{{ $t('Preview') }}</span>
                            </div>
                            <div class="text-xs text-gray-600 font-medium line-clamp-3">
                                {{ findBodyText(item.metadata) }}
                            </div>
                        </div>

                        <!-- Last Updated -->
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-4 border-2 border-blue-100">
                            <div class="flex items-center gap-2">
                                <Calendar class="w-4 h-4 text-blue-600" />
                                <span class="text-sm font-semibold text-gray-700">{{ $t('Last updated') }}</span>
                            </div>
                            <div class="text-sm text-gray-600 font-medium mt-1">
                                {{ item.updated_at }}
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 flex gap-2">
                        <Link v-if="item.status == 'APPROVED' || item.status == 'REJECTED' || item.status == 'PAUSED'"
                            :href="'/templates/' + item.uuid"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-primary/90 hover:bg-primary text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                        <Edit class="w-4 h-4" />
                        <span>{{ $t('Edit') }}</span>
                        </Link>
                        <button @click="openAlert(item.uuid)"
                            class="px-4 py-3 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                            <Trash2 class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- List View (Table) -->
        <div v-if="props.viewMode === 'list' && rows.data.length > 0"
            class="bg-white rounded-3xl shadow-xl border-2 border-primary/10 p-4 overflow-hidden">
            <div class="overflow-x-auto">
                <Table :rows="rows">
                    <TableHeader>
                        <TableHeaderRow>
                            <TableHeaderRowItem :position="'first'"
                                class="text-black font-bold uppercase text-xs tracking-wider py-5">
                                {{ $t('Name') }}
                            </TableHeaderRowItem>
                            <TableHeaderRowItem
                                class="hidden md:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                                {{ $t('Category') }}
                            </TableHeaderRowItem>
                            <TableHeaderRowItem
                                class="hidden lg:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                                {{ $t('Preview') }}
                            </TableHeaderRowItem>
                            <TableHeaderRowItem class="text-black font-bold uppercase text-xs tracking-wider py-5">
                                {{ $t('Status') }}
                            </TableHeaderRowItem>
                            <TableHeaderRowItem
                                class="hidden sm:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                                {{ $t('Last updated') }}
                            </TableHeaderRowItem>
                            <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
                        </TableHeaderRow>
                    </TableHeader>
                    <TableBody>
                        <TableBodyRow v-for="(item, index) in rows.data" :key="index"
                            :class="['group hover:bg-orange-50 transition-all duration-300', !isLastRow(index) ? 'border-b-2 border-primary/10' : '']">

                            <TableBodyRowItem :position="'first'" class="py-6">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary/10 to-orange-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <FileText class="w-6 h-6 text-primary" />
                                    </div>
                                    <span
                                        class="font-bold text-gray-900 group-hover:text-primary transition-colors duration-300 text-base">
                                        {{ item.name }}
                                    </span>
                                </div>
                            </TableBodyRowItem>

                            <TableBodyRowItem class="hidden md:table-cell py-6">
                                <span :class="getCategoryColor(item.category)"
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium">
                                    {{ capitalizeFirstLetter(item.category) }}
                                </span>
                            </TableBodyRowItem>

                            <TableBodyRowItem class="hidden lg:table-cell py-6">
                                <div class="flex items-center gap-2 max-w-[10vw] 2xl:max-w-xs mr-2">
                                    <div
                                        class="px-3 py-2 bg-gradient-to-r from-gray-50 to-orange-50 rounded-lg border-2 border-primary/10 truncate text-xs text-gray-600 font-medium">
                                        {{ findBodyText(item.metadata) }}
                                    </div>
                                </div>
                            </TableBodyRowItem>

                            <TableBodyRowItem class="py-6">
                                <span :class="[
                                    'inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold capitalize shadow-md',
                                    getStatusConfig(item.status).bg,
                                    getStatusConfig(item.status).text
                                ]">
                                    <span class="w-2 h-2 rounded-full bg-white mr-2 animate-pulse"></span>
                                    {{ capitalizeFirstLetter(item.status) }}
                                </span>
                            </TableBodyRowItem>

                            <TableBodyRowItem class="hidden sm:table-cell py-6">
                                <div class="flex items-center gap-2 text-sm text-gray-600 font-medium">
                                    <Calendar class="w-4 h-4 text-gray-400" />
                                    {{ item.updated_at }}
                                </div>
                            </TableBodyRowItem>

                            <TableBodyRowItem :position="'last'" class="py-6">
                                <Dropdown :align="'right'">
                                    <button
                                        class="p-3 hover:bg-primary/10 rounded-xl transition-all duration-300 group/btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="text-gray-400 group-hover/btn:text-primary">
                                            <circle cx="12" cy="12" r="1" />
                                            <circle cx="12" cy="5" r="1" />
                                            <circle cx="12" cy="19" r="1" />
                                        </svg>
                                    </button>
                                    <template #items>
                                        <DropdownItemGroup>
                                            <DropdownItem
                                                v-if="item.status == 'APPROVED' || item.status == 'REJECTED' || item.status == 'PAUSED'"
                                                :href="'/templates/' + item.uuid" class="flex items-center gap-2">
                                                <Edit class="w-4 h-4" />
                                                {{ $t('edit') }}
                                            </DropdownItem>
                                            <DropdownItem as="button" @click="openAlert(item.uuid)"
                                                class="flex items-center gap-2 text-red-600">
                                                <Trash2 class="w-4 h-4" />
                                                {{ $t('Delete') }}
                                            </DropdownItem>
                                        </DropdownItemGroup>
                                    </template>
                                </Dropdown>
                            </TableBodyRowItem>
                        </TableBodyRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="rows.data.length == 0" class="mt-8">
            <div class="bg-white rounded-3xl shadow-2xl border-2 border-primary/10 p-16">
                <div class="max-w-lg mx-auto text-center">
                    <!-- Animated Illustration -->
                    <div class="mb-8 relative inline-block">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-primary via-orange-400 to-red-400 rounded-full blur-3xl opacity-30 animate-pulse">
                        </div>
                        <div class="relative">
                            <div
                                class="bg-gradient-to-br from-primary/10 via-orange-100 to-red-100 p-10 rounded-[2.5rem] inline-block">
                                <FileText class="w-20 h-20 text-primary" stroke-width="1.5" />
                            </div>
                        </div>
                    </div>

                    <h3 class="text-3xl font-bold text-gray-900 mb-4">
                        {{ $t('You don\'t have any templates') }}
                    </h3>
                    <p class="text-gray-600 text-lg mb-10 max-w-md mx-auto">
                        Get started by creating your first message template to streamline your communication
                    </p>

                    <Link href="/templates/create"
                        class="inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-primary via-orange-600 to-red-600 text-white text-lg font-bold rounded-2xl shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </div>
                    <span>{{ $t('Create template') }}</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Alert Modal Component-->
        <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
            :description="$t('Are you sure you want to delete this row? This action can not be undone')" />
    </div>
</template>

<style scoped>
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.4;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>