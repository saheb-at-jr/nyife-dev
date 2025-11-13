<!-- <template>
    <AppLayout>
        <div class="md:bg-inherit bg-white md:flex md:flex-grow capitalize">
            <div class="md:w-[30%] flex-col h-full bg-white border-r border-l md:flex"
                :class="$page.url === '/contacts/add' || contact ? 'hidden' : ''">
                <div class="px-4 pt-4">
                    <div class="flex justify-between mt-2">
                        <div class="flex space-x-1 text-xl">
                            <h2>{{ $t('Contacts') }}</h2>
                            <span class="text-slate-500">{{ props.rowCount }}</span>
                        </div>
                        <div class="flex space-x-2 items-center">
                            <Link href="/contacts/add" title="Add Contact">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
                                <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                                    <path
                                        d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12Zm10-8a8 8 0 1 0 0 16a8 8 0 0 0 0-16Z" />
                                    <path
                                        d="M13 7a1 1 0 1 0-2 0v4H7a1 1 0 1 0 0 2h4v4a1 1 0 1 0 2 0v-4h4a1 1 0 1 0 0-2h-4V7Z" />
                                </g>
                            </svg>
                            </Link>
                        </div>
                    </div>
                </div>
                <ContactTable :rows="props.rows" :filters="props.filters" :type="'contact'" @callback="handleContact" />
            </div>
            <div class="md:w-[70%] bg-cover md:h-[100vh] md:overflow-y-hidden">
                <div v-if="contact">
                    <ContactInfo v-if="!editContact" class="pt-20" :contact="contact" :fields="props.fields"
                        :locationSettings="locationSettings" />
                    <ContactForm v-else :contactGroups="props.contactGroups" :contact="props.contact"
                        :fields="props.fields" :locationSettings="locationSettings" />
                </div>
                <div v-else>
                    <div v-if="$page.url === '/contacts/add'">
                        <ContactForm :contactGroups="props.contactGroups" :contact="props.contact"
                            :fields="props.fields" :locationSettings="locationSettings" />
                    </div>
                    <div v-else>
                        <div class="md:flex justify-center pt-20 hidden">
                            <div class="border pt-20 py-10 w-[30em] rounded-xl bg-white">
                                <h2 class="text-center text-2xl text-slate-500 mb-6">{{ $t('Select contact') }}</h2>
                                <div class="flex justify-center">
                                    <div class="border-r border-slate-500 h-10"></div>
                                </div>
                                <h2 class="text-center text-slate-600">{{ $t('Or') }}</h2>
                                <div class="flex justify-center">
                                    <div class="border-r border-slate-500 h-10"></div>
                                </div>
                                <div class="flex justify-center space-x-4 mt-6">
                                    <Link href="/contacts/add"
                                        class="bg-primary rounded-lg text-sm text-white p-2 px-8 text-center">{{ $t(`Add
                                    contact`) }}</Link>
                                    <button @click="isOpenModal = true"
                                        class="bg-primary rounded-lg text-sm text-white p-2 px-8 text-center">{{
                                            $t('Bulk upload') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <ContactImportModal :type="'contact'" v-model:modelValue="isOpenModal" />
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { Link } from "@inertiajs/vue3";
import ContactForm from '@/Components/ContactComponents/CreateForm.vue';
import ContactImportModal from '@/Components/ContactImportModal.vue';
import ContactInfo from '@/Components/ContactInfo.vue';
import ContactTable from '@/Components/Tables/ContactTable.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ rows: Object, filters: Object, rowCount: Number, contactGroups: Object, contact: Object, editContact: Boolean, fields: Object, locationSettings: String, flash: Object });
const isOpenModal = ref(false);

const handleContact = (value) => {
    router.visit('/contacts', {
        method: 'get',
        data: value,
    })
}
</script> -->


<!-- ========================================== NEW UI CODE ==================================== -->

<template>
    <AppLayout>
        <div class="h-[calc(100vh-65px)] bg-gradient-to-br from-gray-50 to-orange-50/30">
            <div class="md:flex md:h-full">
                <!-- Sidebar -->
                <div class="md:w-[380px] bg-white shadow-xl md:flex flex-col"
                    :class="$page.url === '/contacts/add' || contact ? 'hidden' : ''">

                    <!-- Header -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between mb-0">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $t('Contacts') }}</h1>
                                <p class="text-sm text-gray-500 mt-1">{{ props.rowCount }} {{ $t('total contacts') }}
                                </p>
                            </div>
                            <Link href="/contacts/add"
                                class="group relative overflow-hidden bg-[#ff5100] hover:bg-[#e64900] text-white p-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105"
                                title="Add Contact">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                class="relative z-10">
                                <path fill="currentColor"
                                    d="M12 4a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2h-6v6a1 1 0 1 1-2 0v-6H5a1 1 0 1 1 0-2h6V5a1 1 0 0 1 1-1Z" />
                            </svg>
                            </Link>
                        </div>
                    </div>

                    <!-- Contact Table -->
                    <ContactTable :rows="props.rows" :filters="props.filters" :type="'contact'"
                        @callback="handleContact" />
                </div>

                <!-- Main Content Area -->
                <div class="flex-1 md:overflow-hidden">
                    <div v-if="contact" class="h-full">
                        <ContactInfo v-if="!editContact" :contact="contact" :fields="props.fields"
                            :locationSettings="locationSettings" />
                        <ContactForm v-else :contactGroups="props.contactGroups" :contact="props.contact"
                            :fields="props.fields" :locationSettings="locationSettings" />
                    </div>

                    <div v-else-if="$page.url === '/contacts/add'" class="h-full">
                        <ContactForm :contactGroups="props.contactGroups" :contact="props.contact"
                            :fields="props.fields" :locationSettings="locationSettings" />
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
                                            d="M12 12q-1.65 0-2.825-1.175T8 8q0-1.65 1.175-2.825T12 4q1.65 0 2.825 1.175T16 8q0 1.65-1.175 2.825T12 12Zm-8 8v-2.8q0-.85.438-1.563T5.6 14.55q1.55-.775 3.15-1.163T12 13q1.65 0 3.25.388t3.15 1.162q.725.375 1.163 1.088T20 17.2V20H4Z" />
                                    </svg>
                                </div>
                            </div>

                            <h2 class="text-3xl font-bold text-gray-900 mb-3">{{ $t('Select contact') }}</h2>

                            <p class="text-gray-500 mb-8">{{ $t('Choose a contact from the list or create a new one') }}
                            </p>

                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/contacts/add"
                                    class="group relative overflow-hidden bg-[#ff5100] hover:bg-[#e64900] text-white px-8 py-3.5 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                                <span class="relative z-10 flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M12 4a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2h-6v6a1 1 0 1 1-2 0v-6H5a1 1 0 1 1 0-2h6V5a1 1 0 0 1 1-1Z" />
                                    </svg>
                                    {{ $t('Add contact') }}
                                </span>
                                </Link>

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

    <ContactImportModal :type="'contact'" v-model:modelValue="isOpenModal" />
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { Link } from "@inertiajs/vue3";
import ContactForm from '@/Components/ContactComponents/CreateForm.vue';
import ContactImportModal from '@/Components/ContactImportModal.vue';
import ContactInfo from '@/Components/ContactInfo.vue';
import ContactTable from '@/Components/Tables/ContactTable.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    rows: Object,
    filters: Object,
    rowCount: Number,
    contactGroups: Object,
    contact: Object,
    editContact: Boolean,
    fields: Object,
    locationSettings: String,
    flash: Object
});

const isOpenModal = ref(false);

const handleContact = (value) => {
    router.visit('/contacts', {
        method: 'get',
        data: value,
    })
}
</script>