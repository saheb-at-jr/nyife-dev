<!-- <script setup>
    import { Link } from "@inertiajs/vue3";
    import { ref } from 'vue';
    import debounce from 'lodash/debounce';
    import { router, useForm } from '@inertiajs/vue3';
    import AlertModal from '@/Components/AlertModal.vue';
    import { useAlertModal } from '@/Composables/useAlertModal';
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

    const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

    const form = useForm({'test': null});

    const deleteAction = (key) => {
        form.delete('/campaigns/' + key);
    }
    
    const params = ref({
        search: props.filters.search,
    });

    const isLastRow = (index) => {
      return index === props.rows.data.length - 1;
    }

    const isSearching = ref(false);
    const emit = defineEmits(['delete']);

    const formatPercentageRate = (numeratorCount, contactCount, contactGroupCount) => {
        if(contactCount > 0){
            return (numeratorCount/contactCount * 100).toFixed(2) + '%';
        } else {
            return (numeratorCount/contactGroupCount * 100).toFixed(2) + '%';
        }
    };

    const formatStats = (numeratorCount, contactCount, contactGroupCount) => {
        if(contactCount > 0){
            return numeratorCount + '/' + contactCount;
        } else {
            return numeratorCount + '/' + contactGroupCount;
        }
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
        router.visit('/campaigns', {
            method: 'get',
            data: params.value,
        })
    }
</script>
<template>
    <div class="bg-[#f0f2f5] md:bg-white flex items-center md:border md:border-primary md:border-none md:shadow-sm h-12 md:h-10 w-full md:w-80 rounded-xl mb-6 text-sm mt-6">
        <span class="pl-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z"/></svg>
        </span>
        <input @input="search" v-model="params.search" type="text" class="outline-none px-4 w-full bg-inherit" :placeholder="$t('Search campaigns')">
        <button v-if="isSearching === false && params.search" @click="clearSearch" type="button" class="pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z"/></svg>
        </button>
        <span v-if="isSearching" class="pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0"><animateTransform attributeName="transform" calcMode="discrete" dur="2.4s" repeatCount="indefinite" type="rotate" values="0 12 12;90 12 12;180 12 12;270 12 12"/><animate attributeName="opacity" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite" values="1;1;0"/></circle><circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0"><animateTransform attributeName="transform" begin="0.2s" calcMode="discrete" dur="2.4s" repeatCount="indefinite" type="rotate" values="30 12 12;120 12 12;210 12 12;300 12 12"/><animate attributeName="opacity" begin="0.2s" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite" values="1;1;0"/></circle><circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0"><animateTransform attributeName="transform" begin="0.4s" calcMode="discrete" dur="2.4s" repeatCount="indefinite" type="rotate" values="60 12 12;150 12 12;240 12 12;330 12 12"/><animate attributeName="opacity" begin="0.4s" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite" values="1;1;0"/></circle></svg>
        </span>
    </div>
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'">{{ $t('Campaign name') }}</TableHeaderRowItem>
                <TableHeaderRowItem class="hidden md:table-cell">{{ $t('Template') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Delivery rate') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Read rate') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Status') }}</TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                <TableBodyRowItem :position="'first'">
                    <Link :href="'/campaigns/' + item.uuid" class="block w-full">
                        {{ item.name }}
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden md:table-cell">
                    <Link :href="'/campaigns/' + item.uuid" class="block w-full">
                        {{ item.template.name }}
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <Link :href="'/campaigns/' + item.uuid" class="block w-full">
                        <div v-if="item.status == 'scheduled'">
                            {{ $t('N/A') }}
                        </div>
                        <div v-else>
                            <span class="bg-slate-200 px-1 py-1 rounded-lg mr-1 hidden md:inline-block">
                                {{ formatPercentageRate(item.delivery_count, item.contacts_count, item.contact_group_count) }}
                            </span>
                            {{ formatStats(item.delivery_count, item.contacts_count, item.contact_group_count) }}
                        </div>
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">
                    <Link :href="'/campaigns/' + item.uuid" class="block w-full">
                        <div v-if="item.status == 'scheduled'">
                            {{ $t('N/A') }}
                        </div>
                        <div v-else>
                            <span class="bg-slate-200 px-1 py-1 rounded-lg mr-1 hidden md:inline-block">
                                {{ formatPercentageRate(item.read_count, item.contacts_count, item.contact_group_count) }}
                            </span>
                            {{ formatStats(item.read_count, item.contacts_count, item.contact_group_count) }}
                        </div>
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem>
                    <Link :href="'/campaigns/' + item.uuid" class="flex items-center w-full capitalize">
                        {{ item.status }}
                    </Link>
                </TableBodyRowItem>
                <TableBodyRowItem :position="'last'">
                    <Dropdown :align="'right'" class="mt-2">
                        <button class="inline-flex w-full justify-center rounded-md text-sm font-medium text-black hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                            <span class="hover:bg-[#F6F7F9] hover:rounded-full w-[fit-content] p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z"/>
                                </svg>
                            </span>
                        </button>
                        <template #items>
                            <DropdownItemGroup>
                                <DropdownItem :href="'/campaigns/' + item.uuid">{{ $t('View') }}</DropdownItem>
                                <DropdownItem as="button" @click="openAlert(item.uuid)">{{ $t('Delete') }}</DropdownItem>
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
                <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 256 256"><path fill="currentColor" d="M216 80h-32V48a16 16 0 0 0-16-16H40a16 16 0 0 0-16 16v128a8 8 0 0 0 13 6.22L72 154v30a16 16 0 0 0 16 16h93.59L219 230.22a8 8 0 0 0 5 1.78a8 8 0 0 0 8-8V96a16 16 0 0 0-16-16M66.55 137.78L40 159.25V48h128v88H71.58a8 8 0 0 0-5.03 1.78M216 207.25l-26.55-21.47a8 8 0 0 0-5-1.78H88v-32h80a16 16 0 0 0 16-16V96h32Z"></path></svg>
            </div>
            <h3 class="text-center text-lg font-medium mb-4">{{ $t('You don\'t have any campaigns') }}</h3>
            <div class="flex justify-center">
                <Link href="/campaigns/create" class="rounded-md px-3 py-2 text-sm hover:shadow-md text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 bg-primary" :disabled="isLoading">
                    <span v-if="!isLoading">{{ $t('Create campaign') }}</span>
                </Link>
            </div>
        </div>
    </div>

    <AlertModal 
        v-model="isOpenAlert" 
        @confirm="() => confirmAlert(deleteAction)"
        :label = "$t('Delete row')" 
        :description = "$t('Are you sure you want to delete this campaign? This action will only delete the campaign; sent messages will not be deleted from the chat history.')"
    />
</template>
   -->



<!-- ============================================= NEW UI CODE ============================================= -->

<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router, useForm } from '@inertiajs/vue3';
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
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

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();
const form = useForm({ 'test': null });

const deleteAction = (key) => {
    form.delete('/campaigns/' + key);
}

const params = ref({
    search: props.filters.search,
});

const isLastRow = (index) => {
    return index === props.rows.data.length - 1;
}

const isSearching = ref(false);
const emit = defineEmits(['delete']);

const formatPercentageRate = (numeratorCount, contactCount, contactGroupCount) => {
    if (contactCount > 0) {
        return (numeratorCount / contactCount * 100).toFixed(2) + '%';
    } else {
        return (numeratorCount / contactGroupCount * 100).toFixed(2) + '%';
    }
};

const formatStats = (numeratorCount, contactCount, contactGroupCount) => {
    if (contactCount > 0) {
        return numeratorCount + '/' + contactCount;
    } else {
        return numeratorCount + '/' + contactGroupCount;
    }
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
    router.visit('/campaigns', {
        method: 'get',
        data: params.value,
    })
}

const getStatusConfig = (status) => {
    const configs = {
        'scheduled': {
            bg: 'bg-gradient-to-r from-amber-500 to-orange-500',
            text: 'text-white',
            icon: 'M12 6v6l4 2'
        },
        'completed': {
            bg: 'bg-gradient-to-r from-emerald-500 to-teal-500',
            text: 'text-white',
            icon: 'M20 6L9 17l-5-5'
        },
        'draft': {
            bg: 'bg-gradient-to-r from-slate-500 to-gray-500',
            text: 'text-white',
            icon: 'M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'
        },
        'active': {
            bg: 'bg-gradient-to-r from-blue-500 to-cyan-500',
            text: 'text-white',
            icon: 'M22 11.08V12a10 10 0 1 1-5.93-9.14'
        },
        'paused': {
            bg: 'bg-gradient-to-r from-purple-500 to-pink-500',
            text: 'text-white',
            icon: 'M10 9v6m4-6v6'
        }
    };
    return configs[status] || configs['draft'];
}

const viewMode = ref('list'); // 'grid' or 'list'
</script>

<template>

    <!-- Search and Filter Bar -->
    <div class="mb-6 space-y-4">
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
            <!-- Search Bar -->
            <div class="relative flex-1 max-w-2xl w-full">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                        class="text-primary/40">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                </div>
                <input @input="search" v-model="params.search" type="text"
                    class="w-full pl-14 pr-14 py-5 bg-white border-2 border-primary/10 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-base shadow-lg"
                    :placeholder="$t('Search campaigns')">

                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <button v-if="isSearching === false && params.search" @click="clearSearch" type="button"
                        class="p-2 hover:bg-primary/30 rounded-xl transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-primary">
                            <circle cx="12" cy="12" r="10" />
                            <path d="m15 9-6 6M9 9l6 6" />
                        </svg>
                    </button>
                    <div v-if="isSearching" class="animate-spin">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                            class="text-primary">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="self-end flex justify-center items-center flex-wrap gap-4">
                <!-- Create Campaign Button -->
                <div class="">
                    <Link href="/campaigns/create"
                        class="max-w-min text-nowrap bg-primary/90 hover:bg-primary rounded-xl px-4 py-3  text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    {{ $t('Create campaign') }}</Link>
                </div>
                <!-- View Toggle -->
                <div class="flex items-center gap-2 bg-white rounded-2xl p-1.5 shadow-lg border-2 border-violet-100">
                    <button @click="viewMode = 'list'" :class="[
                        'px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300',
                        viewMode === 'list' ? 'bg-primary text-white shadow-lg' : 'text-gray-600 hover:bg-gray-50'
                    ]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="inline-block">
                            <line x1="8" y1="6" x2="21" y2="6" />
                            <line x1="8" y1="12" x2="21" y2="12" />
                            <line x1="8" y1="18" x2="21" y2="18" />
                            <line x1="3" y1="6" x2="3.01" y2="6" />
                            <line x1="3" y1="12" x2="3.01" y2="12" />
                            <line x1="3" y1="18" x2="3.01" y2="18" />
                        </svg>
                    </button>
                    <button @click="viewMode = 'grid'" :class="[
                        'px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300',
                        viewMode === 'grid' ? 'bg-primary text-white shadow-lg' : 'text-gray-600 hover:bg-gray-50'
                    ]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="inline-block">
                            <rect x="3" y="3" width="7" height="7" />
                            <rect x="14" y="3" width="7" height="7" />
                            <rect x="14" y="14" width="7" height="7" />
                            <rect x="3" y="14" width="7" height="7" />
                        </svg>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Grid View -->
    <div v-if="viewMode === 'grid' && rows.data.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="(item, index) in rows.data" :key="index"
            class="group bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border-2 border-transparent hover:border-violet-200 transform hover:-translate-y-2">

            <!-- Card Header with Status -->
            <div :class="['p-6 pb-4', getStatusConfig(item.status).bg]">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                            <span
                                :class="['text-xs font-bold uppercase tracking-wider', getStatusConfig(item.status).text]">
                                {{ item.status }}
                            </span>
                        </div>
                        <h3 class="text-white font-bold text-xl line-clamp-2 group-hover:underline">
                            <Link :href="'/campaigns/' + item.uuid">
                            {{ item.name }}
                            </Link>
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
                                <DropdownItem :href="'/campaigns/' + item.uuid">{{ $t('View') }}</DropdownItem>
                                <DropdownItem as="button" @click="openAlert(item.uuid)">{{ $t('Delete') }}
                                </DropdownItem>
                            </DropdownItemGroup>
                        </template>
                    </Dropdown>
                </div>

                <div class="flex items-center gap-2 text-white/90 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                    <span class="font-medium">{{ item.template.name }}</span>
                </div>
            </div>

            <!-- Card Body with Stats -->
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Delivery Rate -->
                    <div
                        class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-4 border-2 border-emerald-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-emerald-600">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                    <polyline points="22 4 12 14.01 9 11.01" />
                                </svg>
                                {{ $t('Delivery rate') }}
                            </span>
                            <span v-if="item.status !== 'scheduled'" class="text-lg font-bold text-emerald-700">
                                {{ formatPercentageRate(item.delivery_count, item.contacts_count,
                                    item.contact_group_count) }}
                            </span>
                            <span v-else class="text-sm text-gray-400 italic">{{ $t('N/A') }}</span>
                        </div>
                        <div v-if="item.status !== 'scheduled'" class="text-xs text-gray-600 font-medium">
                            {{ formatStats(item.delivery_count, item.contacts_count, item.contact_group_count) }}
                        </div>
                    </div>

                    <!-- Read Rate -->
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-4 border-2 border-blue-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-blue-600">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                {{ $t('Read rate') }}
                            </span>
                            <span v-if="item.status !== 'scheduled'" class="text-lg font-bold text-blue-700">
                                {{ formatPercentageRate(item.read_count, item.contacts_count, item.contact_group_count)
                                }}
                            </span>
                            <span v-else class="text-sm text-gray-400 italic">{{ $t('N/A') }}</span>
                        </div>
                        <div v-if="item.status !== 'scheduled'" class="text-xs text-gray-600 font-medium">
                            {{ formatStats(item.read_count, item.contacts_count, item.contact_group_count) }}
                        </div>
                    </div>
                </div>

                <!-- View Button -->
                <Link :href="'/campaigns/' + item.uuid"
                    class="mt-4 w-full flex items-center justify-center gap-2 px-4 py-3 bg-primary/90 hover:bg-primary text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                <span>{{ $t('View Details') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6" />
                </svg>
                </Link>
            </div>
        </div>
    </div>

    <!-- List View (Table) -->
    <div v-if="viewMode === 'list' && rows.data.length > 0"
        class="bg-white rounded-3xl shadow-xl border-2 border-primary/10 p-4 overflow-hidden">
        <div class="overflow-x-auto">
            <Table :rows="rows">
                <TableHeader>
                    <TableHeaderRow>
                        <TableHeaderRowItem :position="'first'"
                            class="text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('Campaign name') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem
                            class="hidden md:table-cell text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('Template') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem class="text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('Delivery rate') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem class="text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('Read rate') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem class="text-black font-bold uppercase text-xs tracking-wider py-5">
                            {{ $t('Status') }}
                        </TableHeaderRowItem>
                        <TableHeaderRowItem :position="'last'" class="">
                        </TableHeaderRowItem>
                    </TableHeaderRow>
                </TableHeader>
                <TableBody>
                    <TableBodyRow v-for="(item, index) in rows.data" :key="index"
                        :class="['group hover:bg-violet-50 transition-all duration-300', !isLastRow(index) ? 'border-b-2 border-violet-50' : '']">

                        <TableBodyRowItem :position="'first'" class="py-6">
                            <Link :href="'/campaigns/' + item.uuid" class="block w-full">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-100 to-purple-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="text-violet-600">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                    </svg>
                                </div>
                                <span
                                    class="font-bold text-gray-900 group-hover:text-violet-600 transition-colors duration-300 text-base">
                                    {{ item.name }}
                                </span>
                            </div>
                            </Link>
                        </TableBodyRowItem>

                        <TableBodyRowItem class="hidden md:table-cell py-6">
                            <Link :href="'/campaigns/' + item.uuid" class="block w-full">
                            <span
                                class="text-gray-600 font-medium group-hover:text-gray-900 transition-colors duration-300">
                                {{ item.template.name }}
                            </span>
                            </Link>
                        </TableBodyRowItem>

                        <TableBodyRowItem class="hidden sm:table-cell py-6">
                            <Link :href="'/campaigns/' + item.uuid" class="block w-full">
                            <div v-if="item.status == 'scheduled'" class="text-gray-400 italic font-medium">
                                {{ $t('N/A') }}
                            </div>
                            <div v-else class="flex items-center gap-3">
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-bold bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-md">
                                    {{ formatPercentageRate(item.delivery_count, item.contacts_count,
                                        item.contact_group_count) }}
                                </span>
                                <span class="text-sm text-gray-600 font-semibold">
                                    {{ formatStats(item.delivery_count, item.contacts_count, item.contact_group_count)
                                    }}
                                </span>
                            </div>
                            </Link>
                        </TableBodyRowItem>

                        <TableBodyRowItem class="hidden sm:table-cell py-6">
                            <Link :href="'/campaigns/' + item.uuid" class="block w-full">
                            <div v-if="item.status == 'scheduled'" class="text-gray-400 italic font-medium">
                                {{ $t('N/A') }}
                            </div>
                            <div v-else class="flex items-center gap-3">
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-bold bg-gradient-to-r from-blue-500 to-cyan-500 text-white shadow-md">
                                    {{ formatPercentageRate(item.read_count, item.contacts_count,
                                        item.contact_group_count)
                                    }}
                                </span>
                                <span class="text-sm text-gray-600 font-semibold">
                                    {{ formatStats(item.read_count, item.contacts_count, item.contact_group_count) }}
                                </span>
                            </div>
                            </Link>
                        </TableBodyRowItem>

                        <TableBodyRowItem class="py-6">
                            <Link :href="'/campaigns/' + item.uuid" class="flex items-center w-full">
                            <span
                                :class="['inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold capitalize shadow-md', getStatusConfig(item.status).bg, getStatusConfig(item.status).text]">
                                <span class="w-2 h-2 rounded-full bg-white mr-2 animate-pulse"></span>
                                {{ item.status }}
                            </span>
                            </Link>
                        </TableBodyRowItem>

                        <TableBodyRowItem :position="'last'" class="py-6">
                            <Dropdown :align="'right'">
                                <button class="p-3 hover:bg-violet-50 rounded-xl transition-all duration-300 group/btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="text-gray-400 group-hover/btn:text-violet-600">
                                        <circle cx="12" cy="12" r="1" />
                                        <circle cx="12" cy="5" r="1" />
                                        <circle cx="12" cy="19" r="1" />
                                    </svg>
                                </button>
                                <template #items>
                                    <DropdownItemGroup>
                                        <DropdownItem :href="'/campaigns/' + item.uuid">{{ $t('View') }}</DropdownItem>
                                        <DropdownItem as="button" @click="openAlert(item.uuid)">{{ $t('Delete') }}
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
        <div class="bg-white rounded-3xl shadow-2xl border-2 border-violet-100 p-16">
            <div class="max-w-lg mx-auto text-center">
                <!-- Animated Illustration -->
                <div class="mb-8 relative inline-block">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-violet-400 via-purple-400 to-fuchsia-400 rounded-full blur-3xl opacity-30 animate-pulse">
                    </div>
                    <div class="relative">
                        <div
                            class="bg-gradient-to-br from-violet-100 via-purple-100 to-fuchsia-100 p-10 rounded-[2.5rem] inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="text-violet-600">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                <path d="M8 10h.01M12 10h.01M16 10h.01" />
                            </svg>
                        </div>
                    </div>
                </div>

                <h3 class="text-3xl font-bold text-gray-900 mb-4">
                    {{ $t('You don\'t have any campaigns') }}
                </h3>
                <p class="text-gray-600 text-lg mb-10 max-w-md mx-auto">
                    Get started by creating your first campaign to engage with your audience effectively
                </p>

                <Link href="/campaigns/create"
                    class="inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-violet-600 via-purple-600 to-fuchsia-600 text-white text-lg font-bold rounded-2xl shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                </div>
                <span>{{ $t('Create campaign') }}</span>
                </Link>
            </div>
        </div>
    </div>

    <!-- Alert Modal -->
    <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this campaign? This action will only delete the campaign; sent messages will not be deleted from the chat history.')" />
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
</style>