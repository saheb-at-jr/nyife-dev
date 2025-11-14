<!-- <template>
    <SettingLayout :aimodule="aimodule" :fbmodule="fbmodule">
        <div class="md:h-[90vh]">
            <div class="flex justify-center items-center">
                <div class="md:w-[60em]">
                    <div class="bg-white border border-slate-200 rounded-lg py-2 text-sm pb-4 px-4 mb-20">
                        <div class="w-full py-2 mb-2 mt-2">
                            <div class="flex w-full mb-4">
                                <div class="text-md">
                                    <h4 class="text-[16px]">{{ $t('Basic Replies') }}</h4>
                                    <span class="flex items-center mt-1 text-slate-500">
                                        {{ $t('Respond automatically to messages based on your own criteria') }}
                                    </span>
                                </div>
                                <div class="ml-auto">
                                    <Link href="/automation/basic/create"
                                        class="float-right rounded-md bg-primary px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    {{ $t('Create') }}</Link>
                                </div>
                            </div>
                            <div class="w-5/5">
                                <AutoReplyTable :rows="props.rows" :filters="props.filters" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SettingLayout>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
import SettingLayout from "./../Layout.vue";
import AutoReplyTable from '@/Components/Tables/AutoReplyTable.vue';

const props = defineProps(['rows', 'filters', 'aimodule', 'fbmodule']);
</script> -->

<!-- ================================================== NEW UI CODE ================================================== -->

<template>
    <SettingLayout :aimodule="aimodule" :fbmodule="fbmodule">
        <div>
            <!-- Header Section -->
            <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
                <div class="mb-4 md:mb-0">
                    <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $t('Basic Replies') }}</h4>
                    <p class="text-sm text-gray-600 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            class="text-[#ff5100]">
                            <path fill="currentColor"
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                        </svg>
                        <span class="ml-2">{{ $t('Respond automatically to messages based on your own criteria')
                        }}</span>
                    </p>
                </div>

                <Link href="/automation/basic/create"
                    class="ml-auto inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff7a3d] text-white font-medium rounded-xl shadow-lg transition-all duration-200 hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="mr-2">
                    <path fill="currentColor" d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2v-6Z" />
                </svg>
                {{ $t('Create') }}
                </Link>
            </div>

            <!-- Table Component -->
            <AutoReplyTable :rows="props.rows" :filters="props.filters" />
        </div>
    </SettingLayout>
</template>

<script setup>
import SettingLayout from "./../Layout.vue";
import { Link } from "@inertiajs/vue3";
import AutoReplyTable from '@/Components/Tables/AutoReplyTable.vue';

const props = defineProps(['rows', 'filters', 'aimodule', 'fbmodule']);
</script>