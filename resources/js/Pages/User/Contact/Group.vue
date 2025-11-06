<!-- <template>
    <AppLayout>
        <div class="bg-white md:bg-inherit md:flex md:flex-grow capitalize">
            <div class="md:w-[30%] md:flex flex-col h-full bg-white border-r border-l" :class="group ? 'hidden' : ''">
                <div class="px-4 pt-4">
                    <div class="flex justify-between mt-2">
                        <div class="flex space-x-1 text-xl">
                            <h2>{{ $t('Groups') }}</h2>
                            <span class="text-slate-500 ">{{ props.rowCount }}</span>
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span @click="openModal()" class="cursor-pointer" title="Add Contact">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
                                    <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                                        <path
                                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12Zm10-8a8 8 0 1 0 0 16a8 8 0 0 0 0-16Z" />
                                        <path
                                            d="M13 7a1 1 0 1 0-2 0v4H7a1 1 0 1 0 0 2h4v4a1 1 0 1 0 2 0v-4h4a1 1 0 1 0 0-2h-4V7Z" />
                                    </g>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <ContactTable :rows="props.rows" :filters="props.filters" :type="'group'" @callback="handleGroup" />
            </div>
            <div class="md:w-[70%] bg-cover md:h-[100vh] flex justify-center overflow-y-scroll ">
                <div v-if="group">
                    <ContactGroupInfo :group="group" />
                </div>
                <div v-else class="md:block pt-20 hidden">
                    <div class="border py-10 w-[30em] rounded-xl bg-white">
                        <h2 class="text-center text-2xl text-slate-500 mb-6">{{ $t('Select group') }}</h2>
                        <div class="flex justify-center">
                            <div class="border-r border-slate-500 h-10"></div>
                        </div>
                        <h2 class="text-center text-slate-600">{{ $t('Or') }}</h2>
                        <div class="flex justify-center">
                            <div class="border-r border-slate-500 h-10"></div>
                        </div>
                        <div class="flex justify-center space-x-4 mt-6">
                            <button @click="openModal()"
                                class="bg-primary rounded-lg text-sm text-white p-2 px-8 text-center capitalize">{{
                                    $t('Add group') }}</button>
                            <button @click="isOpenModal = true"
                                class="bg-primary rounded-lg text-sm text-white p-2 px-8 text-center">{{ $t(`Bulk
                                upload`) }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
    <FormModal v-model="isOpenFormModal" :label="$t('Add group')" :url="formUrl" :form="form" :formInputs="formInputs"
        @callback="handleCallback" />

    <ContactGroupImportModal :type="'group'" v-model:modelValue="isOpenModal" />
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import ContactGroupInfo from '@/Components/ContactGroupInfo.vue';
import ContactGroupImportModal from '@/Components/ContactImportModal.vue';
import ContactTable from '@/Components/Tables/ContactTable.vue';
import FormModal from '@/Components/FormModal.vue';
import { router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({ rows: Object, filters: Object, rowCount: Number, group: Object });
const isOpenModal = ref(false);
const currentUrl = window.location.href;
const isOpenFormModal = ref(false);
const formUrl = ref(currentUrl);
const form = ref({
    name: '',
});

const initialFormInputs = [
    {
        inputType: 'FormInput',
        name: 'name',
        label: trans('name'),
        type: 'text',
        className: 'sm:col-span-6',
    },
];

const formInputs = initialFormInputs;

const openModal = () => {
    isOpenFormModal.value = true;
    form.value.name = '';
}

const handleGroup = (value) => {
    router.visit('/contact-groups', {
        method: 'get',
        data: value,
    })
}

const handleCallback = (res) => {
    group.value = res.data;
    form.value.name = res.data.name;
}
</script> -->


<!-- ========================================== NEW UI CODE ==================================== -->

<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50/30">
            <div class="md:flex md:h-screen">
                <!-- Sidebar -->
                <div class="md:w-[380px] bg-white shadow-xl md:flex flex-col" :class="group ? 'hidden' : ''">

                    <!-- Header -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $t('Groups') }}</h1>
                                <p class="text-sm text-gray-500 mt-1">{{ props.rowCount }} {{ $t('total groups') }}</p>
                            </div>
                            <button @click="openModal()"
                                class="group relative overflow-hidden bg-[#ff5100] hover:bg-[#e64900] text-white p-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105"
                                title="Add Group">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    class="relative z-10">
                                    <path fill="currentColor"
                                        d="M12 4a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2h-6v6a1 1 0 1 1-2 0v-6H5a1 1 0 1 1 0-2h6V5a1 1 0 0 1 1-1Z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Contact Table -->
                    <ContactTable :rows="props.rows" :filters="props.filters" :type="'group'" @callback="handleGroup" />
                </div>

                <!-- Main Content Area -->
                <div class="flex-1 md:overflow-hidden">
                    <div v-if="group" class="h-full">
                        <ContactGroupInfo :group="group" />
                    </div>

                    <!-- Empty State -->
                    <div v-else class="hidden md:flex items-center justify-center h-full p-8">
                        <div class="text-center max-w-md">
                            <div class="mb-8 relative">
                                <div
                                    class="w-32 h-32 mx-auto bg-gradient-to-br from-[#ff5100]/20 to-orange-100 rounded-full flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                                        class="text-[#ff5100]">
                                        <path fill="currentColor"
                                            d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5S5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05c1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                    </svg>
                                </div>
                            </div>

                            <h2 class="text-3xl font-bold text-gray-900 mb-3">{{ $t('Select group') }}</h2>
                            <p class="text-gray-500 mb-8">{{ $t('Choose a group from the list or create a new one') }}
                            </p>

                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <button @click="openModal()"
                                    class="group relative overflow-hidden bg-[#ff5100] hover:bg-[#e64900] text-white px-8 py-3.5 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                                    <span class="relative z-10 flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M12 4a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2h-6v6a1 1 0 1 1-2 0v-6H5a1 1 0 1 1 0-2h6V5a1 1 0 0 1 1-1Z" />
                                        </svg>
                                        {{ $t('Add group') }}
                                    </span>
                                </button>

                                <button @click="isOpenModal = true"
                                    class="bg-white border-2 border-[#ff5100] text-[#ff5100] hover:bg-[#ff5100] hover:text-white px-8 py-3.5 rounded-xl font-medium transition-all duration-300 shadow-md hover:shadow-lg">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M11 16V7.85l-2.6 2.6L7 9l5-5l5 5l-1.4 1.45l-2.6-2.6V16h-2Zm-5 4q-.825 0-1.413-.588T4 18v-3h2v3h12v-3h2v3q0 .825-.588 1.413T18 20H6Z" />
                                        </svg>
                                        {{ $t('Bulk upload') }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Form Modal Component-->
    <FormModal v-model="isOpenFormModal" :label="$t('Add group')" :url="formUrl" :form="form" :formInputs="formInputs"
        @callback="handleCallback" />

    <ContactGroupImportModal :type="'group'" v-model:modelValue="isOpenModal" />
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import ContactGroupInfo from '@/Components/ContactGroupInfo.vue';
import ContactGroupImportModal from '@/Components/ContactImportModal.vue';
import ContactTable from '@/Components/Tables/ContactTable.vue';
import FormModal from '@/Components/FormModal.vue';
import { router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({ rows: Object, filters: Object, rowCount: Number, group: Object });
const isOpenModal = ref(false);
const currentUrl = window.location.href;
const isOpenFormModal = ref(false);
const formUrl = ref(currentUrl);
const form = ref({
    name: '',
});

const initialFormInputs = [
    {
        inputType: 'FormInput',
        name: 'name',
        label: trans('name'),
        type: 'text',
        className: 'sm:col-span-6',
    },
];

const formInputs = initialFormInputs;

const openModal = () => {
    isOpenFormModal.value = true;
    form.value.name = '';
}

const handleGroup = (value) => {
    router.visit('/contact-groups', {
        method: 'get',
        data: value,
    })
}

const handleCallback = (res) => {
    group.value = res.data;
    form.value.name = res.data.name;
}
</script>