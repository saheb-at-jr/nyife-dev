<!-- <template>
    <AppLayout>
        <div class="p-4 md:p-8 rounded-[5px] h-full overflow-y-auto">
            <div class="flex justify-between capitalize">
                <div>
                    <h2 class="text-xl mb-1">{{ $t('Campaign details') }}</h2>
                    <p class="mb-6 flex items-center text-sm leading-6">
                        <span class="ml-1 mt-1">{{ $t('Ref') }}: {{ props.campaign.uuid }}</span>
                    </p>
                </div>
                <div class="space-x-2">
                    <a :href="'/campaigns/export/' + props.campaign.uuid"
                        class="rounded-md bg-secondary px-3 py-2 text-sm text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        {{ $t('Export as CSV') }}
                    </a>

                    <Link href="/campaigns"
                        class="rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    {{ $t('Back') }}
                    </Link>
                </div>
            </div>

            <div class="md:flex md:space-x-4">
                <div class="md:w-[70%] capitalize">
                    <div class="flex w-[100%] mb-8 rounded-lg">
                        <div class="w-full rounded-tl-lg rounded-bl-lg text-center bg-white py-8 border">
                            <h2 class="text-xl">{{ props.campaign.total_message_count }}</h2>
                            <h4 class="text-sm">{{ $t('Messages') }}</h4>
                        </div>
                        <div class="w-full text-center bg-white py-8 border">
                            <h2 class="text-xl">{{ props.campaign.total_sent_count }}</h2>
                            <h4 class="text-sm">{{ $t('Sent') }}</h4>
                        </div>
                        <div class="w-full text-center bg-white py-8 border">
                            <h2 class="text-xl">{{ props.campaign.total_delivered_count }}</h2>
                            <h4 class="text-sm">{{ $t('Delivered') }}</h4>
                        </div>
                        <div class="w-full bg-white text-center py-8 border">
                            <h2 class="text-xl">{{ props.campaign.total_read_count }}</h2>
                            <h4 class="text-sm">{{ $t('Read') }}</h4>
                        </div>
                        <div class="w-full rounded-tr-lg rounded-br-lg bg-white text-center py-8 border">
                            <h2 class="text-xl">{{ props.campaign.total_failed_count }}</h2>
                            <h4 class="text-sm">{{ $t('Rejected by meta') }}</h4>
                        </div>

                    </div>

                    <CampaignLogTable :rows="props.rows" :filters="props.filters" :uuid="props.campaign.uuid" />
                </div>
                <div class="md:w-[30%]">
                    <div class="w-full rounded-lg bg-white pt-4 pb-8 border px-4 space-y-1 capitalize">
                        <h2 class="mb-2">{{ $t('Campaign details') }}</h2>
                        <div class="text-sm bg-slate-100 p-3 rounded-lg">
                            <h3>{{ $t('Campaign name') }}</h3>
                            <p>{{ props.campaign?.name }}</p>
                        </div>
                        <div class="text-sm bg-slate-100 p-3 rounded-lg">
                            <h3>{{ $t('Template') }}</h3>
                            <p>{{ props.campaign?.template?.name }}</p>
                        </div>
                        <div class="text-sm bg-slate-100 p-3 rounded-lg">
                            <h3>{{ $t('Recipients') }}</h3>
                            <p>{{ props.campaign.contact_group_id === '0' ? 'All Contacts' :
                                props.campaign?.contact_group?.name }}</p>
                        </div>
                        <div class="text-sm bg-slate-100 p-3 rounded-lg">
                            <h3>{{ $t('Time scheduled') }}</h3>
                            <p>{{ props.campaign.scheduled_at }}</p>
                        </div>
                    </div>

                    <div class="w-full rounded-lg p-5 mt-5 border chat-bg">
                        <WhatsappTemplate :parameters="JSON.parse(props.campaign.metadata)" :placeholder="false"
                            :visible="true" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import CampaignLogTable from '@/Components/Tables/CampaignLogTable.vue';
import WhatsappTemplate from '@/Components/WhatsappTemplate.vue';
import { Link } from "@inertiajs/vue3";
const props = defineProps(['campaign', 'rows', 'filters']);
</script> -->



<!-- ========================================== NEW UI CODE ==================================== -->


<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 p-4 md:p-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#ff5100] to-[#ff7433] flex items-center justify-center shadow-lg shadow-orange-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                            </div>
                            <div>
                                <h1
                                    class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-700 bg-clip-text text-transparent capitalize">
                                    {{ $t('Campaign details') }}
                                </h1>
                                <div class="flex items-center gap-2 mt-1">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="mr-1.5">
                                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                                        </svg>
                                        {{ $t('Ref') }}: {{ props.campaign.uuid }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Link href="/campaigns"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ $t('Back') }}</span>

                        </Link>

                        <a :href="'/campaigns/export/' + props.campaign.uuid"
                            class="group inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:shadow-lg hover:scale-105 active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="transition-transform group-hover:-translate-y-0.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="7 10 12 15 17 10" />
                                <line x1="12" y1="15" x2="12" y2="3" />
                            </svg>
                            {{ $t('Export as CSV') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content Area (70%) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                        <div class="group relative">
                            <div
                                class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                            </div>
                            <div
                                class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/10">

                                <div class="flex flex-col items-start text-center">
                                    <!-- <div
                                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                        </svg>
                                    </div> -->
                                    <div
                                        class="mb-3 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl p-3 shadow-md">
                                        <MessageSquare class="w-6 h-6 text-white" />
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{
                                        props.campaign.total_message_count }}</h2>
                                    <h4 class="text-xs text-gray-600 font-medium capitalize">{{
                                        $t('Messages') }}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <div class="group relative">
                            <div
                                class="absolute -inset-0.5 bg-gradient-to-r from-purple-400 to-pink-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                            </div>
                            <div
                                class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-purple-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-purple-500/10">
                                <div class="flex flex-col items-start text-center">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-[#9333ea] flex items-center justify-center mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="22" y1="2" x2="11" y2="13" />
                                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{
                                        props.campaign.total_sent_count }}</h2>
                                    <h4 class="text-xs text-gray-600 font-medium capitalize">{{ $t('Sent') }}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <div class="group relative">
                            <div
                                class="absolute -inset-0.5 bg-gradient-to-r from-emerald-400 to-green-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                            </div>
                            <div
                                class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-emerald-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-emerald-500/10">
                                <div class="flex flex-col items-start text-center">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{
                                        props.campaign.total_delivered_count }}</h2>
                                    <h4 class="text-xs text-gray-600 font-medium capitalize">{{
                                        $t('Delivered')
                                        }}</h4>
                                </div>
                            </div>

                        </div>

                        <div class="group relative">
                            <div
                                class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                            </div>
                            <div
                                class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/10">
                                <div class="flex flex-col items-start text-center">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-[#0891b2] flex items-center justify-center mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{
                                        props.campaign.total_read_count }}</h2>
                                    <h4 class="text-xs text-gray-600 font-medium capitalize">{{ $t('Read') }}
                                    </h4>
                                </div>
                            </div>
                        </div>


                        <div class="group relative">
                            <div
                                class="absolute -inset-0.5 bg-primary rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
                            </div>
                            <div
                                class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-orange-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-primary/10">
                                <div class="flex flex-col items-start text-center">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-[#dc2626] flex items-center justify-center mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <line x1="15" y1="9" x2="9" y2="15" />
                                            <line x1="9" y1="9" x2="15" y2="15" />
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{
                                        props.campaign.total_failed_count }}</h2>
                                    <h4 class="text-xs text-gray-600 font-medium capitalize">{{ $t(`Rejected
                                        by meta`) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Log Table -->
                    <div
                        class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                        <CampaignLogTable :rows="props.rows" :filters="props.filters" :uuid="props.campaign.uuid" />
                    </div>
                </div>

                <!-- Sidebar (30%) -->
                <div class="space-y-6">
                    <!-- Campaign Details Card -->
                    <div
                        class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-900 capitalize flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="16" x2="12" y2="12" />
                                    <line x1="12" y1="8" x2="12.01" y2="8" />
                                </svg>
                                {{ $t('Campaign details') }}
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div
                                class="group bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100 hover:shadow-md transition-all duration-300">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xs font-semibold text-blue-700 tracking-wide mb-1 capitalize">
                                            {{ $t('Campaign name') }}</h3>
                                        <p class="text-sm font-medium text-gray-900 break-words capitalize">{{
                                            props.campaign?.name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="group bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100 hover:shadow-md transition-all duration-300">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-purple-500 flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xs font-semibold text-purple-700 tracking-wide mb-1 capitalize">
                                            {{ $t('Template') }}</h3>
                                        <p class="text-sm font-medium text-gray-900 break-words capitalize">{{
                                            props.campaign?.template?.name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="group bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100 hover:shadow-md transition-all duration-300">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xs font-semibold text-green-700 tracking-wide mb-1 capitalize">
                                            {{ $t('Recipients') }}</h3>
                                        <p class="text-sm font-medium text-gray-900 break-words capitalize">
                                            {{ props.campaign.contact_group_id === '0' ? 'All Contacts' :
                                                props.campaign?.contact_group?.name }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="group bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-4 border border-orange-100 hover:shadow-md transition-all duration-300">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-[#ff5100] flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xs font-semibold text-orange-700 tracking-wide mb-1 capitalize">
                                            {{ $t('Time scheduled') }}</h3>
                                        <p class="text-sm font-medium text-gray-900 break-words">{{
                                            props.campaign.scheduled_at }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Template Preview Card -->
                    <div
                        class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-900 capitalize flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                </svg>
                                {{ $t('Template Preview') }}
                            </h2>
                        </div>
                        <div class="p-5 chat-bg">
                            <WhatsappTemplate :parameters="JSON.parse(props.campaign.metadata)" :placeholder="false"
                                :visible="true" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import CampaignLogTable from '@/Components/Tables/CampaignLogTable.vue';
import WhatsappTemplate from '@/Components/WhatsappTemplate.vue';
import { Link } from "@inertiajs/vue3";
import { ArrowLeft, MessageSquare } from "lucide-vue-next";

const props = defineProps(['campaign', 'rows', 'filters']);
</script>