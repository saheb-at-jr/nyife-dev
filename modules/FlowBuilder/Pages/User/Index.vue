<!-- <template>
    <SettingLayout :aimodule="aimodule" :fbmodule="fbmodule">
        <div class="md:h-[90vh]">
            <div class="flex justify-center items-center">
                <div class="md:w-[60em]">
                    <div class="bg-white border border-slate-200 rounded-lg py-2 text-sm pb-4 px-4 mb-20">
                        <div class="w-full py-2 mb-2 mt-2">
                            <div class="flex w-full mb-4">
                                <div class="text-md">
                                    <h4 class="text-[16px]">{{ $t('My Automations') }}</h4>
                                    <span class="flex items-center mt-1 text-slate-500">
                                        {{ $t('Respond automatically to messages based on your own criteria') }}
                                    </span>
                                </div>
                                <div class="ml-auto">
                                    <button @click="isOpenFormModal = true;"
                                        class="float-right rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{
                                        $t('New Automation') }}</button>
                                </div>
                            </div>
                            <div class="w-5/5">
                                <FlowsTable :rows="props.rows" :filters="props.filters" />
                                <div class="px-4 pb-4">
                                    <Pagination class="mt-3" :pagination="props.rows.meta" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :label="$t('Create Automation')" :isOpen=isOpenFormModal>
            <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4">
                <form @submit.prevent="submitForm()" class="grid gap-x-6 gap-y-4 sm:grid-cols-6">
                    <FormInput v-model="form.name" :error="form.errors.name" :name="$t('Name')" :type="'text'"
                        :class="'sm:col-span-6'" />
                    <FormTextArea v-model="form.description" :error="form.errors.description" :name="$t('Description')"
                        :class="'sm:col-span-6'" />

                    <div class="mt-4 flex">
                        <button type="button" @click.self="isOpenFormModal = false"
                            class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{
                            $t('Cancel') }}</button>
                        <button
                            :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': form.processing }]"
                            :disabled="form.processing">
                            <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                    opacity=".5" />
                                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                    <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                        repeatCount="indefinite" to="360 12 12" type="rotate" />
                                </path>
                            </svg>
                            <span v-else>{{ $t('Save') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </SettingLayout>
</template>
<script setup>
import SettingLayout from "./../../../../resources/js/Pages/User/Automation/Layout.vue";
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import FlowsTable from '@modules/FlowBuilder/Pages/User/Components/FlowsTable.vue';
import FormInput from '@/Components/FormInput.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps(['rows', 'filters', 'aimodule', 'fbmodule']);
const isOpenFormModal = ref(false);

const form = useForm({
    name: null,
    description: null
});

const submitForm = () => {
    form.post('/automation/flows', {
        preserveScroll: true,
        onSuccess: () => {
            router.visit('/automation/ai', {
                preserveState: false,
            });
        }
    })
}
</script> -->


<!-- ================================================== NEW UI CODE ================================================== -->

<template>
    <SettingLayout :aimodule="aimodule" :fbmodule="fbmodule">
        <div class="">
            <!-- Header Section with Gradient Background -->
            <div class="relative mb-8">
                <div class="relative flex items-center justify-between flex-wrap gap-4">
                    <div class="mb-4 md:mb-0">
                        <div class="flex items-center space-x-3 mb-2">
                            <div>
                                <h4 class="text-xl font-bold text-gray-900">{{ $t('My Automations') }}</h4>
                                <p class="text-sm text-gray-600 flex items-center mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        class="text-[#ff5100] mr-2">
                                        <path fill="currentColor"
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                    </svg>
                                    {{ $t('Respond automatically to messages based on your own criteria') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <button @click="isOpenFormModal = true"
                        class="ml-auto inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] text-white font-semibold rounded-xl hover:scale-105 transition-transform duration-300 group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            class="mr-2 group-hover:rotate-90 transition-transform duration-300">
                            <path fill="currentColor" d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2v-6Z" />
                        </svg>
                        {{ $t('New Automation') }}
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8">
                <!-- Total Automations -->
                <div class="group relative">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                    </div>
                    <div
                        class="relative bg-white border border-gray-200 rounded-2xl p-4 hover:border-blue-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/10">
                        <div class="flex justify-between items-start">
                            <div class="bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl p-3 shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                                </svg>
                            </div>
                            <div
                                class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    class="text-emerald-600">
                                    <path fill="currentColor"
                                        d="m3 17l6-6l4 4l8-8l-1.41-1.41L13 12.17L9 8.17L1.59 15.59z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-gray-900 text-3xl font-bold my-2">{{ props.rows.data.length }}</p>
                        <p class="text-gray-600 text-sm font-medium">Total Automations</p>

                    </div>
                </div>

                <!-- Total Runs -->
                <div class="group relative">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-green-400 to-emerald-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                    </div>
                    <div
                        class="relative bg-white border border-gray-200 rounded-2xl p-4 hover:border-green-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-green-500/10">
                        <div class="flex justify-between items-start">
                            <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl p-3 shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89l.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7s-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42A8.954 8.954 0 0 0 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54l.72-1.21l-3.5-2.08V8H12z" />
                                </svg>
                            </div>
                            <div
                                class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    class="text-emerald-600">
                                    <path fill="currentColor"
                                        d="m3 17l6-6l4 4l8-8l-1.41-1.41L13 12.17L9 8.17L1.59 15.59z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-gray-900 text-3xl font-bold my-2">{{props.rows.data.reduce((sum, item) => sum +
                            item.flow_logs_count, 0)}}</p>
                        <p class="text-gray-600 text-sm font-medium">Total Runs</p>

                    </div>
                </div>

                <!-- Active Automations -->
                <div class="group relative">
                    <div
                        class="absolute -inset-0.5 bg-primary rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                    </div>
                    <div
                        class="relative bg-white border border-gray-200 rounded-2xl p-4 hover:border-orange-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-primary/10">
                        <div class="flex justify-between items-start">
                            <div class="bg-primary rounded-xl p-3 shadow-md shadow-primary/30">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="m10.6 16.6l7.05-7.05l-1.4-1.4l-5.65 5.65l-2.85-2.85l-1.4 1.4l4.25 4.25ZM12 22q-2.075 0-3.9-.788t-3.175-2.137q-1.35-1.35-2.137-3.175T2 12q0-2.075.788-3.9t2.137-3.175q1.35-1.35 3.175-2.137T12 2q2.075 0 3.9.788t3.175 2.137q1.35 1.35 2.138 3.175T22 12q0 2.075-.788 3.9t-2.137 3.175q-1.35 1.35-3.175 2.138T12 22Z" />
                                </svg>
                            </div>
                            <div
                                class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    class="text-emerald-600">
                                    <path fill="currentColor"
                                        d="m3 17l6-6l4 4l8-8l-1.41-1.41L13 12.17L9 8.17L1.59 15.59z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-gray-900 text-3xl font-bold my-2">{{props.rows.data.filter(item => item.status
                            === 'active').length}}</p>
                        <p class="text-gray-600 text-sm font-medium">Active</p>

                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-hidden">
                <FlowsTable :rows="props.rows" :filters="props.filters" />
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <Pagination :pagination="props.rows.meta" />
            </div>
        </div>

        <!-- Create Automation Modal -->
        <Modal :label="$t('Create Automation')" :isOpen="isOpenFormModal">
            <div class="mt-5">
                <form @submit.prevent="submitForm()" class="space-y-5">
                    <!-- Modal Header with Icon -->
                    <div class="flex items-center space-x-3 pb-4 border-b border-gray-200">
                        <div
                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                class="text-white">
                                <path fill="currentColor"
                                    d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $t('New Automation') }}</h3>
                            <p class="text-sm text-gray-500">Create a new automation workflow</p>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="space-y-4">
                        <FormInput v-model="form.name" :error="form.errors.name" :name="$t('Name')" :type="'text'"
                            :class="'w-full'" />
                        <FormTextArea v-model="form.description" :error="form.errors.description"
                            :name="$t('Description')" :class="'w-full'" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" @click.self="isOpenFormModal = false"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">
                            {{ $t('Cancel') }}
                        </button>
                        <button type="submit"
                            :class="['inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] text-white font-medium rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 transition-all duration-200', { 'opacity-50 cursor-not-allowed': form.processing }]"
                            :disabled="form.processing">
                            <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" class="mr-2 animate-spin">
                                <path fill="currentColor"
                                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                    opacity=".5" />
                                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                    <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                        repeatCount="indefinite" to="360 12 12" type="rotate" />
                                </path>
                            </svg>
                            <span>{{ form.processing ? $t('Creating...') : $t('Save') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </SettingLayout>
</template>

<script setup>
import SettingLayout from "./../../../../resources/js/Pages/User/Automation/Layout.vue";
import { ref } from 'vue';
import { useForm, router } from "@inertiajs/vue3";
import FlowsTable from '@modules/FlowBuilder/Pages/User/Components/FlowsTable.vue';
import FormInput from '@/Components/FormInput.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps(['rows', 'filters', 'aimodule', 'fbmodule']);

const isOpenFormModal = ref(false);

const form = useForm({
    name: null,
    description: null
});

const submitForm = () => {
    form.post('/automation/flows', {
        preserveScroll: true,
        onSuccess: () => {
            isOpenFormModal.value = false;
            form.reset();
            router.visit('/automation/flows', {
                preserveState: false,
            });
        }
    })
}
</script>
