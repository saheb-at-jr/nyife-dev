<template>
    <AppLayout>
        <div
            class="bg-white md:bg-inherit pt-0 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] overflow-y-scroll capitalize">
            <div class="flex justify-between mt-8 md:mt-0 flex-wrap gap-4">
                <!-- <div>
                    <h2 class="text-xl mb-1">{{ $t('Campaigns') }}</h2>
                    <p class="mb-6 md:flex items-center text-sm leading-6 text-gray-600 hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span class="ml-1 mt-1">{{ $t('Add campaigns') }}</span>
                    </p>
                </div> -->

                <div class="mb-3 w-full">

                    <div class="w-full flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center shadow-lg shadow-[#ff5100]/25">
                                    <Megaphone class="w-6 h-6 text-white" />
                                </div>
                                {{ $t('Campaigns') }}
                            </h1>
                            <p class="text-gray-600">{{ $t('Manage your campaigns') }}</p>
                        </div>
                        <div class="flex justify-end mb-6">
                            <div
                                class="inline-flex bg-white border border-gray-200 rounded-2xl shadow-md overflow-hidden">
                                <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                                    class="flex items-center gap-2 px-6 py-3 font-semibold text-sm transition-all duration-300"
                                    :class="activeTab === tab.id
                                        ? 'bg-[#ff5100] text-white shadow-inner'
                                        : 'text-gray-600 hover:bg-gray-50'">
                                    <component :is="tab.icon" class="w-5 h-5"
                                        :class="activeTab === tab.id ? 'text-white' : 'text-[#ff5100] opacity-80'" />
                                    {{ tab.label }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <CampaignTable v-if="activeTab === 'whatsapp'" :rows="props.rows" :filters="props.filters" />
            <div v-else-if="activeTab === 'rcs'"
                class="p-6 bg-white border border-gray-100 rounded-2xl shadow-md text-gray-400 italic">
                RCS content coming soon...
            </div>

            <div v-else-if="activeTab === 'sms'"
                class="p-6 bg-white border border-gray-100 rounded-2xl shadow-md text-gray-400 italic">
                SMS content coming soon...
            </div>
        </div>
    </AppLayout>
</template>

<script setup>

import AppLayout from "./../Layout/App.vue";
import CampaignTable from '@/Components/Tables/CampaignTable.vue';
import { Megaphone } from "lucide-vue-next";
import { ref } from "vue";
const props = defineProps(['rows', 'filters']);
import { MessageCircle, MessageSquare, MessageSquareText } from 'lucide-vue-next'

const activeTab = ref('whatsapp')

const tabs = [
    { id: 'whatsapp', label: 'WhatsApp', icon: MessageCircle },
    { id: 'rcs', label: 'RCS', icon: MessageSquare },
    { id: 'sms', label: 'SMS', icon: MessageSquareText },
]

</script>
