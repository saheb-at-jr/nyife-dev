<!-- <script setup>
import { ref } from 'vue';
import { default as axios } from 'axios';
import ChatBubble from '@/Components/ChatComponents/ChatBubble.vue';

const props = defineProps({
    contactId: {
        type: Number,
        required: true
    },
    initialMessages: {
        type: Array,
        required: true
    },
    hasMoreMessages: {
        type: Boolean,
        required: true
    },
    initialNextPage: {
        type: Number,
        required: true
    }
});

const messages = ref(props.initialMessages);
const loading = ref(false);
const nextPage = ref(props.initialNextPage);
const hasMore = ref(props.hasMoreMessages);

const loadMoreMessages = async () => {
    if (loading.value || !hasMore.value) return;

    loading.value = true;
    try {
        const response = await axios.get(`/chats/${props.contactId}/messages?page=${nextPage.value}`);
        messages.value = [...response.data.messages, ...messages.value];
        hasMore.value = response.data.hasMoreMessages;
        nextPage.value = response.data.nextPage;
    } catch (error) {
        console.error('Error loading messages:', error);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="py-4 md:py-4 relative px-6 md:px-10">
        <div v-if="hasMore" class="text-center py-2">
            <div v-if="loading"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Loading...
            </div>
            <button v-else @click="loadMoreMessages"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                </svg>
                Load More Messages
            </button>
        </div>

        <div v-for="(chat, index) in messages" :key="index" class="flex flex-grow flex-col"
            :class="chat[0].type === 'ticket' ? 'justify-center' : 'justify-end'">
            <ChatBubble v-if="chat[0].type === 'chat'" :content="chat[0].value" :type="chat[0].value.type" />
            <div v-if="chat[0].type === 'ticket'" class="py-2">
                <div class="text-center font-light text-sm border-b border-t py-2 border-dashed border-black">
                    <div>{{ chat[0].value.description }} </div>
                    <div class="text-xs">{{ chat[0].value.created_at }}</div>
                </div>
            </div>
            <div v-if="chat[0].type === 'notes'" class="py-2 bg-orange-100 my-2 rounded-lg p-2 w-[fit-content] ml-auto">
                <div class="text-right font-light text-sm">
                    <div>{{ chat[0].value.content }}</div>
                    <div class="flex items-center justify-between mt-2 space-x-4">
                        <p class="text-gray-500 text-xs text-right leading-none">{{ chat[0].value.created_at }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> -->

<!-- ========================================== NEW UI CODE ==================================== -->

<script setup>
import { ref } from 'vue';
import { default as axios } from 'axios';
import ChatBubble from '@/Components/ChatComponents/ChatBubble.vue';
import { ArrowUpDown } from 'lucide-vue-next';

const props = defineProps({
    contactId: {
        type: Number,
        required: true
    },
    initialMessages: {
        type: Array,
        required: true
    },
    hasMoreMessages: {
        type: Boolean,
        required: true
    },
    initialNextPage: {
        type: Number,
        required: true
    }
});

const messages = ref(props.initialMessages);
const loading = ref(false);
const nextPage = ref(props.initialNextPage);
const hasMore = ref(props.hasMoreMessages);

const loadMoreMessages = async () => {
    if (loading.value || !hasMore.value) return;

    loading.value = true;
    try {
        const response = await axios.get(`/chats/${props.contactId}/messages?page=${nextPage.value}`);
        messages.value = [...response.data.messages, ...messages.value];
        hasMore.value = response.data.hasMoreMessages;
        nextPage.value = response.data.nextPage;
    } catch (error) {
        console.error('Error loading messages:', error);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="py-6 px-4 md:px-10 relative">
        <!-- Load More Button -->
        <div v-if="hasMore" class="flex justify-center py-4">
            <button v-if="loading" disabled
                class="inline-flex items-center gap-2 px-6 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 shadow-sm transition-all">
                <svg class="animate-spin h-5 w-5 text-[#ff5100]" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span>{{ $t('Loading...') }}</span>
            </button>

            <button v-else @click="loadMoreMessages"
                class="inline-flex items-center gap-2 px-6 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 hover:border-[#ff5100] rounded-xl text-sm font-medium text-gray-700 hover:text-[#ff5100] shadow-sm hover:shadow-md transition-all">
                <ArrowUpDown class="w-4 h-4" />
                <span>{{ $t('Load More Messages') }}</span>
            </button>
        </div>

        <!-- Messages -->
        <div v-for="(chat, index) in messages" :key="index" class="mb-2">
            <!-- Chat Message -->
            <ChatBubble v-if="chat[0].type === 'chat'" :content="chat[0].value" :type="chat[0].value.type" />

            <!-- Ticket Event -->
            <div v-if="chat[0].type === 'ticket'" class="flex justify-center my-4">
                <div
                    class="bg-white/90 backdrop-blur-sm border border-gray-200 rounded-xl px-4 py-3 shadow-sm max-w-md">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    class="text-blue-600">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ chat[0].value.description }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ chat[0].value.created_at }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div v-if="chat[0].type === 'notes'" class="flex justify-end my-2">
                <div
                    class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl rounded-tr-none p-4 max-w-md shadow-sm">
                    <div class="flex items-start gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            class="text-amber-600 flex-shrink-0 mt-0.5">
                            <path fill="currentColor"
                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm4 18H6V4h7v5h5v11zm-6-1h4v-2h-4v2zm0-4h4v-2h-4v2zm-4-6h8v2H8V9z" />
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-amber-900 mb-1">{{ $t('Note') }}</p>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ chat[0].value.content }}</p>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <p class="text-xs text-amber-700">{{ chat[0].value.created_at }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Smooth scroll behavior */
.overflow-y-auto {
    scroll-behavior: smooth;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}
</style>