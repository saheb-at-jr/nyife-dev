<template>
    <AppLayout>
        <div class="md:flex md:flex-col bg-white border-l text-[#000] overflow-y-hidden">
            <div class="flex justify-between px-8 py-6 border-b pb-2 capitalize">
                <!-- <div>
                    <h2 class="text-xl mb-1">{{ $t('New campaign') }}</h2>
                    <p class="flex items-center text-sm leading-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span class="ml-1 mt-1">{{ $t('Create campaign') }}</span>
                    </p>
                </div> -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center shadow-lg shadow-[#ff5100]/25">
                            <Megaphone class="w-6 h-6 text-white" />
                        </div>
                        {{ $t('New campaign') }}
                    </h1>
                    <p class="text-gray-600">{{ $t('Create campaign') }}</p>
                </div>
                <div class="space-x-2 flex items-center">
                    <Link href="/campaigns"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span></Link>
                </div>
            </div>
            <CampaignForm :templates="templates" :contactGroups="contactGroups" :settings="settings" />
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import CampaignForm from '@/Components/CampaignForm.vue';
import { Link } from "@inertiajs/vue3";
import { ArrowLeft, Megaphone } from "lucide-vue-next";
import 'vue3-toastify/dist/index.css';
const props = defineProps(['templates', 'contactGroups', 'settings']);
</script>