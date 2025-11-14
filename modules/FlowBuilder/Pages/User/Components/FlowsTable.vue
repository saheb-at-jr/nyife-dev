<!-- <script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
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
    filters: {
        type: Object
    }
});

const { isOpenAlert: isOpenDeleteAlert, openAlert: openDeleteAlert, confirmAlert: confirmDeleteAlert } = useAlertModal();
const { isOpenAlert: isOpenDuplicateAlert, openAlert: openDuplicateAlert, confirmAlert: confirmDuplicateAlert } = useAlertModal();

function handleDeleteFlow(id) {
    openDeleteAlert(id);
}

function handleDuplicateFlow(id) {
    openDuplicateAlert(id);
}

const form = useForm({ 'test': null });

const deleteAction = (key) => {
    form.delete('/automation/flows/' + key);
}

const duplicateAction = (key) => {
    form.get('/automation/flows/duplicate/' + key);
}

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const params = ref({
    search: props.filters.search,
});

const isSearching = ref(false);
const emit = defineEmits(['delete']);

function deleteItem(id) {
    emit('delete', id);
}

const clearSearch = () => {
    params.value.search = null;
    runSearch();
}

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 1000);

const runSearch = () => {
    router.visit('/automation/flows', {
        method: 'get',
        data: params.value,
    })
}
</script>
<template>
    <div
        class="bg-slate-100 md:bg-slate-50 flex items-center border border-primary md:border-none md:shadow-sm h-10 w-full md:w-80 rounded-[0.5rem] mb-6 text-sm">
        <span class="pl-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z" />
            </svg>
        </span>
        <input @input="search" v-model="params.search" type="text"
            class="outline-none px-4 w-full bg-slate-100 md:bg-slate-50" :placeholder="$t('Search by name')">
        <button v-if="isSearching === false && params.search" @click="clearSearch" type="button" class="pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="currentColor"
                    d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z" />
            </svg>
        </button>
        <span v-if="isSearching" class="pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                    <animateTransform attributeName="transform" calcMode="discrete" dur="2.4s" repeatCount="indefinite"
                        type="rotate" values="0 12 12;90 12 12;180 12 12;270 12 12" />
                    <animate attributeName="opacity" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite"
                        values="1;1;0" />
                </circle>
                <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                    <animateTransform attributeName="transform" begin="0.2s" calcMode="discrete" dur="2.4s"
                        repeatCount="indefinite" type="rotate" values="30 12 12;120 12 12;210 12 12;300 12 12" />
                    <animate attributeName="opacity" begin="0.2s" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite"
                        values="1;1;0" />
                </circle>
                <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                    <animateTransform attributeName="transform" begin="0.4s" calcMode="discrete" dur="2.4s"
                        repeatCount="indefinite" type="rotate" values="60 12 12;150 12 12;240 12 12;330 12 12" />
                    <animate attributeName="opacity" begin="0.4s" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite"
                        values="1;1;0" />
                </circle>
            </svg>
        </span>
    </div>
    <div class="bg-slate-100 md:bg-slate-50 rounded-[0.5rem]">
        <table class="w-full">
            <TableHeader>
                <TableHeaderRow>
                    <TableHeaderRowItem :position="'first'">{{ $t('Name') }}</TableHeaderRowItem>
                    <TableHeaderRowItem>{{ $t('Runs') }}</TableHeaderRowItem>
                    <TableHeaderRowItem>{{ $t('Status') }}</TableHeaderRowItem>
                    <TableHeaderRowItem>{{ $t('Last updated') }}</TableHeaderRowItem>
                    <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
                </TableHeaderRow>
            </TableHeader>
            <TableBody>
                <TableBodyRow v-for="(item, index) in rows.data" :key="index"
                    :class="!isLastRow(index) ? 'border-b' : ''">
                    <TableBodyRowItem :position="'first'" class="capitalize">{{ item.name }}</TableBodyRowItem>
                    <TableBodyRowItem class="hidden sm:table-cell">
                        <div
                            class="py-1 px-2 bg-gray-50 rounded-[5px] border border-dashed w-[fit-content] text-xs capitalize">
                            {{ item.flow_logs_count }}
                        </div>
                    </TableBodyRowItem>
                    <TableBodyRowItem class="hidden sm:table-cell">{{ $t(item.status) }}</TableBodyRowItem>
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
                                    <DropdownItem :href="'/automation/flows/' + item.uuid">{{ $t('Edit') }}
                                    </DropdownItem>
                                    <DropdownItem as="button" @click="handleDuplicateFlow(item.uuid)">{{ $t('Copy') }}
                                    </DropdownItem>
                                    <DropdownItem as="button" @click="handleDeleteFlow(item.uuid)">{{ $t('Delete') }}
                                    </DropdownItem>
                                </DropdownItemGroup>
                            </template>
                        </Dropdown>
                    </TableBodyRowItem>
                </TableBodyRow>
            </TableBody>
        </table>
    </div>

    <AlertModal v-model="isOpenDeleteAlert" @confirm="() => confirmDeleteAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')"
        :confirm-button-text="$t('Delete')" confirm-button-class="bg-red-600 hover:bg-red-500" icon="warning" />

    <AlertModal v-model="isOpenDuplicateAlert" @confirm="() => confirmDuplicateAlert(duplicateAction)"
        :label="$t('Duplicate Flow')" :description="$t('Are you sure you want to proceed?')"
        :confirm-button-text="$t('Confirm')" confirm-button-class="bg-red-500 hover:bg-red-500" icon="warning" />
</template> -->



<!-- ============================================= NEW UI CODE ========================================= -->


<template>
    <div class="p-1">
        <!-- Search Bar -->
        <div class="mb-6">
            <div class="relative max-w-md">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        class="text-gray-400">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z" />
                    </svg>
                </div>
                <input @input="search" v-model="params.search" type="text"
                    class="w-full pl-12 pr-12 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl outline-none focus:border-[#ff5100] focus:bg-white transition-all duration-200 text-sm"
                    :placeholder="$t('Search by name')">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button v-if="isSearching === false && params.search" @click="clearSearch" type="button"
                        class="p-1 hover:bg-gray-200 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            class="text-gray-500">
                            <path fill="currentColor"
                                d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z" />
                        </svg>
                    </button>
                    <span v-if="isSearching" class="p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            class="text-[#ff5100] animate-spin">
                            <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                                <animateTransform attributeName="transform" calcMode="discrete" dur="2.4s"
                                    repeatCount="indefinite" type="rotate"
                                    values="0 12 12;90 12 12;180 12 12;270 12 12" />
                                <animate attributeName="opacity" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite"
                                    values="1;1;0" />
                            </circle>
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        <!-- Modern Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-orange-50/50 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{
                            $t('Name') }}</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                            {{ $t('Runs') }}</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden md:table-cell">
                            {{ $t('Status') }}</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden lg:table-cell">
                            {{ $t('Last updated') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <tr v-for="(item, index) in rows.data" :key="index"
                        class="hover:bg-orange-50/30 transition-colors duration-150 group">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 capitalize">{{ item.name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ item.description || 'No description' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <div
                                class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    class="text-blue-600 mr-1.5">
                                    <path fill="currentColor"
                                        d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89l.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7s-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42A8.954 8.954 0 0 0 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z" />
                                </svg>
                                <span class="text-xs font-semibold text-blue-700">{{ item.flow_logs_count }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span v-if="item.status === 'active'"
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-green-100 to-green-200 text-green-700 border border-green-300">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                {{ $t('Active') }}
                            </span>
                            <span v-else
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 border border-gray-300">
                                <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1.5"></span>
                                {{ $t('Inactive') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span class="text-sm text-gray-600">{{ item.updated_at }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <Dropdown :align="'right'">
                                <button
                                    class="inline-flex items-center justify-center w-9 h-9 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors group-hover:bg-orange-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z" />
                                    </svg>
                                </button>
                                <template #items>
                                    <DropdownItemGroup>
                                        <DropdownItem :href="'/automation/flows/' + item.uuid">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" class="mr-2">
                                                <path fill="currentColor"
                                                    d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a.996.996 0 0 0 0-1.41l-2.34-2.34a.996.996 0 0 0-1.41 0l-1.83 1.83l3.75 3.75l1.83-1.83z" />
                                            </svg>
                                            {{ $t('Edit') }}
                                        </DropdownItem>
                                        <DropdownItem as="button" @click="handleDuplicateFlow(item.uuid)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" class="mr-2">
                                                <path fill="currentColor"
                                                    d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z" />
                                            </svg>
                                            {{ $t('Copy') }}
                                        </DropdownItem>
                                        <DropdownItem as="button" @click="handleDeleteFlow(item.uuid)"
                                            class="text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" class="mr-2">
                                                <path fill="currentColor"
                                                    d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                            </svg>
                                            {{ $t('Delete') }}
                                        </DropdownItem>
                                    </DropdownItemGroup>
                                </template>
                            </Dropdown>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Alert Modal -->
    <AlertModal v-model="isOpenDeleteAlert" @confirm="() => confirmDeleteAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')"
        :confirm-button-text="$t('Delete')" confirm-button-class="bg-red-600 hover:bg-red-500" icon="warning" />

    <!-- Duplicate Alert Modal -->
    <AlertModal v-model="isOpenDuplicateAlert" @confirm="() => confirmDuplicateAlert(duplicateAction)"
        :label="$t('Duplicate Flow')" :description="$t('Are you sure you want to proceed?')"
        :confirm-button-text="$t('Confirm')" confirm-button-class="bg-orange-600 hover:bg-orange-500" icon="warning" />
</template>



<script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
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
    filters: {
        type: Object
    }
});

const { isOpenAlert: isOpenDeleteAlert, openAlert: openDeleteAlert, confirmAlert: confirmDeleteAlert } = useAlertModal();
const { isOpenAlert: isOpenDuplicateAlert, openAlert: openDuplicateAlert, confirmAlert: confirmDuplicateAlert } = useAlertModal();

function handleDeleteFlow(id) {
    openDeleteAlert(id);
}

function handleDuplicateFlow(id) {
    openDuplicateAlert(id);
}

const form = useForm({ 'test': null });

const deleteAction = (key) => {
    form.delete('/automation/flows/' + key);
}

const duplicateAction = (key) => {
    form.get('/automation/flows/duplicate/' + key);
}

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const params = ref({
    search: props.filters.search,
});

const isSearching = ref(false);
const emit = defineEmits(['delete']);

function deleteItem(id) {
    emit('delete', id);
}

const clearSearch = () => {
    params.value.search = null;
    runSearch();
}

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 1000);

const runSearch = () => {
    router.visit('/automation/flows', {
        method: 'get',
        data: params.value,
    })
}
</script>