<script setup>
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
import axios from 'axios';

const props = defineProps({
    rows: { type: Object, required: true },
    filters: { type: Object },
    type: { type: String },
    showDeleteBtn: { type: Boolean, default: true },
    showRole: { type: Boolean, default: false }
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();
const form = useForm({ test: null });

const deleteAction = (key) => {
    form.delete(props.type === 'admin' ? '/admin/team/users/' + key : '/admin/users/' + key);
};

const params = ref({ search: props.filters.search });
const isSearching = ref(false);
const emit = defineEmits(['delete']);

function deleteItem(id) {
    emit('delete', id);
}

const clearSearch = () => {
    params.value.search = null;
    runSearch();
};

const isLastRow = (index) => index === props.rows.data.length - 1;

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 1000);

const statusLabel = (status) => {
    if (status === 1) return 'Active';
    if (status === 0) return 'Inactive';
    return status;
};

const runSearch = () => {
    const url = window.location.pathname;
    router.visit(url, { method: 'get', data: params.value });
};

// === Add Balance Modal State ===
const isBalanceModalOpen = ref(false);
const isSuccessModalOpen = ref(false);
const selectedUserId = ref(null);
const balanceForm = useForm({ amount: null });

const openBalanceModal = (id) => {
    selectedUserId.value = id;
    isBalanceModalOpen.value = true;
};

const closeBalanceModal = () => {
    isBalanceModalOpen.value = false;
    balanceForm.reset();
};

const submitBalance = async () => {
    if (!selectedUserId.value) return;

    try {
        await axios.post(`/api/users/${selectedUserId.value}/increment-balance`, {
            amount: balanceForm.amount,
        });

        closeBalanceModal();
        isSuccessModalOpen.value = true;
        runSearch();
    } catch (error) {
        console.error(error);
    }
};

const isDebitModalOpen = ref(false);
const debitForm = useForm({ amount: null });

const openDebitModal = (id) => {
    selectedUserId.value = id;
    isDebitModalOpen.value = true;
};

const closeDebitModal = () => {
    isDebitModalOpen.value = false;
    debitForm.reset();
};

const submitDebit = async () => {
    if (!selectedUserId.value) return;

    try {
        await axios.post(`/api/users/${selectedUserId.value}/decrement-balance`, {
            amount: debitForm.amount,
        });

        closeDebitModal();
        isSuccessModalOpen.value = true;
        runSearch();
    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message || 'Failed to debit balance');
    }
};

// === Balance History Modal State ===
const isHistoryModalOpen = ref(false);
const balanceHistory = ref([]);

const openHistoryModal = async (id) => {
    selectedUserId.value = id;
    isHistoryModalOpen.value = true;

    try {
        const response = await axios.get(`/api/users/${id}/balance-history`);
        balanceHistory.value = response.data.balance_history;
    } catch (error) {
        console.error(error);
    }
};

const closeHistoryModal = () => {
    isHistoryModalOpen.value = false;
    balanceHistory.value = [];
};




// === Marketing/Auth Price Modal State ===
const isPriceModalOpen = ref(false);
const priceForm = useForm({
    marketing_price: null,
    utility_price: null,
    auth_price: null,
});

const openPriceModal = (id) => {
    selectedUserId.value = id;
    isPriceModalOpen.value = true;

    // Optionally preload existing prices
    axios.get(`/api/users/${id}/prices`)
        .then(res => {
            priceForm.marketing_price = res.data.marketing_price;
            priceForm.utility_price = res.data.utility_price;
            priceForm.auth_price = res.data.auth_price;
        })
        .catch(err => console.error(err));
};

const closePriceModal = () => {
    isPriceModalOpen.value = false;
    priceForm.reset();
};

const submitPrice = async () => {
    if (!selectedUserId.value) return;
    const payload = {
        marketing_price: priceForm.marketing_price,
        utility_price: priceForm.utility_price,
        auth_price: priceForm.auth_price,
    }
    console.log(payload);
    
    try {
        await axios.post(`/api/users/${selectedUserId.value}/update-prices`, {
            marketing_price: priceForm.marketing_price,
            utility_price: priceForm.utility_price,
            auth_price: priceForm.auth_price,
        });

        closePriceModal();
        isSuccessModalOpen.value = true;
        runSearch();
    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message || 'Failed to update prices');
    }
};






</script>

<template>
    <!-- Search Bar -->
    <div
        class="md:bg-white flex items-center border border-primary md:border-none md:shadow-sm h-12 md:h-10 md:w-80 rounded-[0.5rem] mb-6 text-xl md:text-sm">
        <span class="pl-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z" />
            </svg>
        </span>
        <input @input="search" v-model="params.search" type="text" class="outline-none px-4 w-full bg-inherit"
            :placeholder="$t('Search users')">
        <button v-if="isSearching === false && params.search" @click="clearSearch" type="button" class="pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="currentColor"
                    d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z" />
            </svg>
        </button>
    </div>

    <!-- Users Table -->
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'">{{ $t('Name') }}</TableHeaderRowItem>
                <TableHeaderRowItem class="hidden sm:table-cell">{{ $t('Email') }}</TableHeaderRowItem>
                <TableHeaderRowItem class="hidden sm:table-cell">{{ $t('Phone') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Balance') }}</TableHeaderRowItem>
                <TableHeaderRowItem v-if="type === 'admin' || showRole === true" class="hidden sm:table-cell">{{
                    $t('Role') }}</TableHeaderRowItem>
                <TableHeaderRowItem>{{ $t('Status') }}</TableHeaderRowItem>
                <TableHeaderRowItem class="hidden sm:table-cell"><span class="float-right">{{ $t('Last updated')
                }}</span></TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                <TableBodyRowItem :position="'first'" class="capitalize">{{ item.full_name }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ item.email }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ item.phone ?? $t('N/A') }}</TableBodyRowItem>
                <TableBodyRowItem>{{ item.balance ?? 0 }}</TableBodyRowItem>
                <TableBodyRowItem v-if="type === 'admin' || showRole === true" class="hidden sm:table-cell capitalize">
                    {{ item.role }}</TableBodyRowItem>
                <TableBodyRowItem class="capitalize">
                    <span class="py-1 rounded-[5px] text-xs px-3 bg-[#ddebf7] text-slate-700">{{
                        statusLabel(item.status) }}</span>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell"><span class="float-right">{{ item.updated_at }}</span>
                </TableBodyRowItem>
                <TableBodyRowItem :position="'last'">
                    <Dropdown v-if="item.role != 'admin'" :align="'right'" class="mt-2">
                        <button
                            class="inline-flex w-full justify-center rounded-md text-sm font-medium text-black hover:bg-opacity-30">
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
                                    :href="type === 'admin' ? '/admin/team/users/' + item.id : '/admin/users/' + item.id">
                                    {{ $t('View/edit') }}</DropdownItem>
                                <DropdownItem as="button" @click="openBalanceModal(item.id)">{{ $t('Add Balance') }}
                                </DropdownItem>
                                <DropdownItem as="button" @click="openDebitModal(item.id)">
                                    {{ $t('Debit Balance') }}
                                </DropdownItem>
                                <DropdownItem as="button" @click="openPriceModal(item.id)">
    {{ $t('Edit Prices') }}
</DropdownItem>

                                <DropdownItem as="button" @click="openHistoryModal(item.id)">{{ $t('Balance History') }}
                                </DropdownItem>
                                <DropdownItem v-if="showDeleteBtn" as="button" @click="openAlert(item.id)">{{
                                    $t('Delete') }}</DropdownItem>
                            </DropdownItemGroup>
                        </template>
                    </Dropdown>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>

    <!-- Delete Modal -->
    <AlertModal v-model="isOpenAlert" @confirm="() => confirmAlert(deleteAction)" :label="$t('Delete row')"
        :description="$t('Are you sure you want to delete this row? This action can not be undone')" />

    <!-- Balance Modal -->
    <div v-if="isBalanceModalOpen" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <div class="bg-white p-6 rounded-xl shadow-xl w-96">
            <h2 class="text-lg font-semibold mb-4">{{ $t('Add Balance') }}</h2>
            <input v-model="balanceForm.amount" type="number" step="0.01" placeholder="Enter amount"
                class="border rounded w-full p-2 mb-4" />
            <div class="flex justify-end space-x-2">
                <button @click="closeBalanceModal" class="px-4 py-2 bg-gray-200 rounded">{{ $t('Cancel') }}</button>
                <button @click="submitBalance" class="px-4 py-2 bg-blue-600 text-white rounded">{{ $t('Add') }}</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <!-- <AlertModal v-model="isSuccessModalOpen" :label="$t('Success')" :description="$t('Balance added successfully ✅')" /> -->

    <!-- Debit Balance Modal -->
    <div v-if="isDebitModalOpen" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <div class="bg-white p-6 rounded-xl shadow-xl w-96">
            <h2 class="text-lg font-semibold mb-4">{{ $t('Debit Balance') }}</h2>
            <input v-model="debitForm.amount" type="number" step="0.01" placeholder="Enter amount"
                class="border rounded w-full p-2 mb-4" />
            <div class="flex justify-end space-x-2">
                <button @click="closeDebitModal" class="px-4 py-2 bg-gray-200 rounded">{{ $t('Cancel') }}</button>
                <button @click="submitDebit" class="px-4 py-2 bg-red-600 text-white rounded">{{ $t('Debit') }}</button>
            </div>
        </div>
    </div>



<!-- Marketing/Auth Prices Modal -->
<div v-if="isPriceModalOpen" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
    <div class="bg-white p-6 rounded-xl shadow-xl w-96">
        <h2 class="text-lg font-semibold mb-4">{{ $t('Edit message Prices') }}</h2>

        <label class="block mb-2">{{ $t('Marketing Price') }}</label>
        <input v-model="priceForm.marketing_price" type="number" step="0.01"
            class="border rounded w-full p-2 mb-4" />


         <label class="block mb-2">{{ $t('Utility Price') }}</label>
        <input v-model="priceForm.utility_price" type="number" step="0.01"
            class="border rounded w-full p-2 mb-4" />


        <label class="block mb-2">{{ $t('Auth Price') }}</label>
        <input v-model="priceForm.auth_price" type="number" step="0.01"
            class="border rounded w-full p-2 mb-4" />

        <div class="flex justify-end space-x-2">
            <button @click="closePriceModal" class="px-4 py-2 bg-gray-200 rounded">{{ $t('Cancel') }}</button>
            <button @click="submitPrice" class="px-4 py-2 bg-green-600 text-white rounded">{{ $t('Save') }}</button>
        </div>
    </div>
</div>







    <!-- Balance History Modal -->
    <div v-if="isHistoryModalOpen" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <div class="bg-white p-6 rounded-xl shadow-xl w-[700px]">
            <h2 class="text-lg font-semibold mb-4">{{ $t('Balance History') }}</h2>
            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 text-left">{{ $t('Date') }}</th>
                        <th class="p-2 text-left">{{ $t('Amount') }}</th>
                        <th class="p-2 text-left">{{ $t('Balance After') }}</th>
                        <th class="p-2 text-left">{{ $t('Type') }}</th>
                        <th class="p-2 text-left">{{ $t('Note') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="record in balanceHistory" :key="record.id" class="border-t">
                        <td class="p-2">{{ new Date(record.created_at).toLocaleString() }}</td>
                        <td class="p-2">{{ record.amount }}</td>
                        <td class="p-2">{{ record.balance_after }}</td>
                        <td class="p-2 capitalize">{{ record.type }}</td>
                        <td class="p-2">{{ record.note ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="flex justify-end mt-4">
                <button @click="closeHistoryModal" class="px-4 py-2 bg-gray-200 rounded">{{ $t('Close') }}</button>
            </div>
        </div>
    </div>
</template>
