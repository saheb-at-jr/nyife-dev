<!-- <script setup>
    import { ref } from 'vue';
    import debounce from 'lodash/debounce';
    import { router } from '@inertiajs/vue3';
    import { useForm } from "@inertiajs/vue3";
    import AlertModal from '@/Components/AlertModal.vue';
    import { useAlertModal } from '@/Composables/useAlertModal';
    import 'vue3-toastify/dist/index.css';
    import TableHeader from '@/Components/TableHeader.vue';
    import TableHeaderRow from '@/Components/TableHeaderRow.vue';
    import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
    import TableBody from '@/Components/TableBody.vue';
    import TableBodyRow from '@/Components/TableBodyRow.vue';
    import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
    import DropdownItem from '@/Components/DropdownItem.vue';
    import Pagination from '@/Components/Pagination.vue';

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
        form.delete('/automation/basic/' + key);
    }

    const isLastRow = (index) => {
      return index === props.rows.data.length - 1;
    }
    
    const params = ref({
        search: props.filters.search,
    });

    const isSearching = ref(false);
    const emit = defineEmits(['delete']);

    const clearSearch = () => {
        params.value.search = null;
        runSearch();
    }

    const search = debounce(() => {
        isSearching.value = true;
        runSearch();
    }, 1000);

    const runSearch = () => {
        router.visit('/automation/basic', {
            method: 'get',
            data: params.value,
        })
    }

    const capitalizeString = (str) => {
        // Check if the string is empty or null
        if (!str) return '';
        
        // Capitalize the first character and concatenate it with the rest of the string
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
</script>
<template>
    <div class="bg-slate-100 md:bg-slate-50 flex items-center border border-primary md:border-none md:shadow-sm h-10 w-full md:w-80 rounded-[0.5rem] mb-6 text-sm">
        <span class="pl-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z"/></svg>
        </span>
        <input @input="search" v-model="params.search" type="text" class="outline-none px-4 w-full bg-slate-100 md:bg-slate-50" :placeholder="$t('Search by name or trigger text')">
        <button v-if="isSearching === false && params.search" @click="clearSearch" type="button" class="pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z"/></svg>
        </button>
        <span v-if="isSearching" class="pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0"><animateTransform attributeName="transform" calcMode="discrete" dur="2.4s" repeatCount="indefinite" type="rotate" values="0 12 12;90 12 12;180 12 12;270 12 12"/><animate attributeName="opacity" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite" values="1;1;0"/></circle><circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0"><animateTransform attributeName="transform" begin="0.2s" calcMode="discrete" dur="2.4s" repeatCount="indefinite" type="rotate" values="30 12 12;120 12 12;210 12 12;300 12 12"/><animate attributeName="opacity" begin="0.2s" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite" values="1;1;0"/></circle><circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0"><animateTransform attributeName="transform" begin="0.4s" calcMode="discrete" dur="2.4s" repeatCount="indefinite" type="rotate" values="60 12 12;150 12 12;240 12 12;330 12 12"/><animate attributeName="opacity" begin="0.4s" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite" values="1;1;0"/></circle></svg>
        </span>
    </div>
    <div class="bg-slate-100 md:bg-slate-50 rounded-[0.5rem]">
        <table class="w-full">
            <TableHeader>
                <TableHeaderRow>
                    <TableHeaderRowItem :position="'first'">{{ $t('Name') }}</TableHeaderRowItem>
                    <TableHeaderRowItem>{{ $t('Trigger text') }}</TableHeaderRowItem>
                    <TableHeaderRowItem>{{ $t('Match criteria') }}</TableHeaderRowItem>
                    <TableHeaderRowItem>{{ $t('Response type') }}</TableHeaderRowItem>
                    <TableHeaderRowItem>{{ $t('Last updated') }}</TableHeaderRowItem>
                    <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
                </TableHeaderRow>
            </TableHeader>
            <TableBody>
                <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                    <TableBodyRowItem :position="'first'" class="capitalize">{{ item.name }}</TableBodyRowItem>
                    <TableBodyRowItem class="hidden sm:table-cell">
                        <div class="py-1 px-2 bg-gray-50 rounded-[5px] border border-dashed w-[10em] truncate text-xs capitalize">
                            {{ item.trigger }}
                        </div>
                    </TableBodyRowItem>
                    <TableBodyRowItem class="hidden sm:table-cell">{{ $t(capitalizeString(item.match_criteria)) }}</TableBodyRowItem>
                    <TableBodyRowItem class="capitalize">
                        <span class="py-1 rounded-[5px] text-xs px-3 bg-[#ddebf7] text-slate-700">{{ $t(JSON.parse(item.metadata).type) }}</span>
                    </TableBodyRowItem>
                    <TableBodyRowItem class="hidden sm:table-cell">{{ item.updated_at }}</TableBodyRowItem>
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
                                    <DropdownItem :href="'/automation/basic/' + item.uuid + '/edit'">{{ $t('Edit') }}</DropdownItem>
                                    <DropdownItem as="button" @click="openAlert(item.uuid)">{{ $t('Delete') }}</DropdownItem>
                                </DropdownItemGroup>
                            </template>
                        </Dropdown>
                    </TableBodyRowItem>
                </TableBodyRow>
            </TableBody>
        </table>
    </div>

    <div class="px-4 pb-4">
        <Pagination class="mt-3" :pagination="rows.meta"/>
    </div>

    <AlertModal 
        v-model="isOpenAlert" 
        @confirm="() => confirmAlert(deleteAction)"
        :label = "$t('Delete row')" 
        :description = "$t('Are you sure you want to delete this row? This action can not be undone')"
    />
</template>
   -->


<!-- ========================================== NEW UI CODE ==================================== -->


<template>
    <div class="space-y-6">
        <!-- Enhanced Search Bar -->
        <div class="relative max-w-md">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    class="text-slate-400">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z" />
                </svg>
            </div>
            <input @input="search" v-model="params.search" type="text"
                class="w-full pl-12 pr-12 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl outline-none focus:border-[#ff5100] focus:bg-white transition-all duration-200 text-sm"
                :placeholder="$t('Search by name or trigger text')">
            <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                <button v-if="isSearching === false && params.search" @click="clearSearch" type="button"
                    class="text-slate-400 hover:text-slate-600 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z" />
                    </svg>
                </button>
                <span v-if="isSearching" class="text-[#ff5100]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        class="animate-spin">
                        <path fill="currentColor"
                            d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                            opacity=".5" />
                        <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z" />
                    </svg>
                </span>
            </div>
        </div>

        <!-- Modern Table Design -->
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-orange-50/50 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            {{ $t('Name') }}
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider hidden md:table-cell">
                            {{ $t('Trigger text') }}
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider hidden lg:table-cell">
                            {{ $t('Match criteria') }}
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            {{ $t('Response type') }}
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider hidden xl:table-cell">
                            {{ $t('Last updated') }}
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            {{ $t('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <tr v-for="(item, index) in rows.data" :key="index"
                        class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-6 py-5">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-slate-900 capitalize">{{ item.name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 hidden md:table-cell">
                            <div
                                class="inline-flex items-center px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium border border-slate-200 max-w-[200px]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    class="mr-1.5 flex-shrink-0">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3s-3-1.34-3-3s1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22c.03-1.99 4-3.08 6-3.08c1.99 0 5.97 1.09 6 3.08c-1.29 1.94-3.5 3.22-6 3.22z" />
                                </svg>
                                <span class="truncate capitalize">{{ item.trigger }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 hidden lg:table-cell">
                            <span class="text-sm text-slate-600 capitalize">{{
                                $t(capitalizeString(item.match_criteria)) }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold" :class="{
                                'bg-blue-100 text-blue-700': JSON.parse(item.metadata).type === 'text',
                                'bg-purple-100 text-purple-700': JSON.parse(item.metadata).type === 'template',
                                'bg-green-100 text-green-700': JSON.parse(item.metadata).type === 'image',
                                'bg-pink-100 text-pink-700': JSON.parse(item.metadata).type === 'audio'
                            }">
                                <span class="w-2 h-2 rounded-full mr-2" :class="{
                                    'bg-blue-500': JSON.parse(item.metadata).type === 'text',
                                    'bg-purple-500': JSON.parse(item.metadata).type === 'template',
                                    'bg-green-500': JSON.parse(item.metadata).type === 'image',
                                    'bg-pink-500': JSON.parse(item.metadata).type === 'audio'
                                }"></span>
                                {{ $t(JSON.parse(item.metadata).type) }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-sm text-slate-600 hidden xl:table-cell">
                            {{ item.updated_at }}
                        </td>
                        <td class="px-6 py-5 text-right absolute z-[60px] right-0">
                            <Dropdown :align="'right'" class="relative inline-block">
                                <button
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z" />
                                    </svg>
                                </button>
                                <template #items>
                                    <DropdownItemGroup>
                                        <DropdownItem :href="'/automation/basic/' + item.uuid + '/edit'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" class="mr-2">
                                                <path fill="currentColor"
                                                    d="M20.71 7.04c.39-.39.39-1.04 0-1.41l-2.34-2.34c-.37-.39-1.02-.39-1.41 0l-1.84 1.83l3.75 3.75M3 17.25V21h3.75L17.81 9.93l-3.75-3.75L3 17.25z" />
                                            </svg>
                                            {{ $t('Edit') }}
                                        </DropdownItem>
                                        <DropdownItem as="button" @click="openAlert(item.uuid)"
                                            class="text-red-600 hover:text-red-700 hover:bg-red-50">
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

            <!-- Empty State -->
            <div v-if="!rows.data || rows.data.length === 0" class="text-center py-16">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                        class="text-slate-400">
                        <path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">No automation rules yet</h3>
                <p class="text-slate-600 mb-6">Create your first automation rule to get started</p>
                <Link href="/automation/basic/create"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff6820] text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="mr-2">
                    <path fill="currentColor" d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2v-6Z" />
                </svg>
                {{ $t('Create') }}
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="rows.data && rows.data.length > 0">
            <Pagination class="mt-6" :pagination="rows.meta" />
        </div>

        <!-- Alert Modal -->
        <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
            :description="$t('Are you sure you want to delete this row? This action can not be undone')" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router, Link } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import Pagination from '@/Components/Pagination.vue';

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
    form.delete('/automation/basic/' + key);
}

const params = ref({
    search: props.filters.search,
});

const isSearching = ref(false);

const clearSearch = () => {
    params.value.search = null;
    runSearch();
}

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 1000);

const runSearch = () => {
    router.visit('/automation/basic', {
        method: 'get',
        data: params.value,
    })
}

const capitalizeString = (str) => {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}
</script>

<style scoped>
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>