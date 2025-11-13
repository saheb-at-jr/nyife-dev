<!-- <script setup>
    import { ref, watchEffect, computed } from 'vue';
    import { router, useForm, Link, usePage } from '@inertiajs/vue3'
    import AlertModal from '@/Components/AlertModal.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
    import DropdownItem from '@/Components/DropdownItem.vue';
    import FormSelectCombo from '@/Components/FormSelectCombo.vue';
    import FormTextArea from '@/Components/FormTextArea.vue';
    import Modal from '@/Components/Modal.vue';
    import { trans } from 'laravel-vue-i18n';

    const props = defineProps(['contact', 'displayContactInfo', 'ticketingIsEnabled', 'ticket', 'addon']);

    const accountUser = computed(() => usePage().props.auth.user);
    const showAlert = ref(false);
    const displayContact = ref(props.displayContactInfo);
    const ticketState = ref(null);
    const isOpenModal = ref(false);
    const user = ref({ 
        'label' : props.ticket?.user ? props.ticket?.user?.full_name  : trans('Unassigned'),
        'value' : props.ticket?.user ? props.ticket?.user?.id  : 0, 
    });

    watchEffect(() => {
        displayContact.value = props.displayContactInfo;
        ticketState.value = props.ticket?.status;
    });

    const emit = defineEmits(['toggleView', 'deleteThread', 'closeThread']);

    const toggleView = () => {
        displayContact.value = !displayContact.value;
        emit('toggleView', displayContact.value);
    }

    const deleteThread = () => {
        router.visit('/chats/' + props.contact.uuid, {
            method: 'delete',
            onFinish: () => {
                showAlert.value = false
            }
        });
    }


    const form2 = useForm({
        'notes': null,
        'contact': null
    });

    const form3 = useForm({
        'ai_assistant': props.contact?.ai_assistance_enabled,
    });

    const changeTicketStatus = (value) => {
        router.put('/tickets/' + props.contact.uuid + '/update', {
            'status' : value
        }, {})
    }

    const changeTicketAgent = () => {
        router.put('/tickets/' + props.contact.uuid + '/assign', {
            'id' : user.value.value
        }, {})
    }

    function loadUsers(query, setOptions) {
        fetch("/team?search=" + query, {
                headers: {
                    'Accept': 'application/json' // Ensure that the request accepts JSON
                }
            })
            .then(response => response.json())
            .then(result => {
                setOptions(result.rows);
            })
            .catch(error => {
                console.error("Error fetching agents:", error);
            });
    }

    const submitForm = () => {
        form2.contact = props.contact.uuid;

        form2.post('/notes', {
            preserveState: false,
            onSuccess: () => {
                form2.reset();
                isOpenModal.value = false;
            }
        });
    }

    const submitForm3 = () => {
        form3.ai_assistant = !form3.ai_assistant;

        form3.post('/automation/contact/' + props.contact.uuid, {
            preserveState: true,
        });
    }
</script>
<template>
    <div class="h-20 bg-white border-b border-1 flex items-center justify-between px-4 md:px-4">
        <div class="flex items-center gap-x-4 cursor-pointer w-3/4">
            <Link href="/chats" class="sm:block md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 24 24"><path fill="currentColor" d="M19 11H7.14l3.63-4.36a1 1 0 1 0-1.54-1.28l-5 6a1.19 1.19 0 0 0-.09.15c0 .05 0 .08-.07.13A1 1 0 0 0 4 12a1 1 0 0 0 .07.36c0 .05 0 .08.07.13a1.19 1.19 0 0 0 .09.15l5 6A1 1 0 0 0 10 19a1 1 0 0 0 .64-.23a1 1 0 0 0 .13-1.41L7.14 13H19a1 1 0 0 0 0-2"/></svg>
            </Link>
            <div @click="toggleView">
                <img v-if="contact.avatar" class="rounded-full w-14 h-14" :src="contact.avatar">
                <div v-else class="rounded-full w-10 h-10 flex items-center justify-center bg-slate-100">{{ contact.full_name.substring(0, 1) }}</div>
            </div>
            <div class="flex items-center w-full gap-x-8">
                <div>
                    <div class="flex items-center" @click="toggleView">
                        <span>{{ contact.full_name }}</span>
                    </div>
                    <div class="w-full flex items-center text-xs">
                        <span @click="toggleView">{{ contact.formatted_phone_number }}</span>
                    </div>
                </div>
                <FormSelectCombo v-if="ticketingIsEnabled && accountUser.teams[0]['role'] != 'agent'" v-model="user" :name="''" :loadOptions="loadUsers" :class="'col-span-1 md:block hidden'" :placeholder="'Select Agent'" @update:modelValue="changeTicketAgent()"/>
            </div>
        </div>
        <div>
            <div class="flex items-center space-x-4">
                <button v-if="ticketState === 'open' && ticketingIsEnabled" @click="changeTicketStatus('closed')" class="text-sm md:inline-flex hidden justify-center rounded-md border border-transparent bg-red-800 px-4 py-1 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">{{ $t('Mark as closed') }}</button>
                <button v-if="ticketState === 'closed' && ticketingIsEnabled" @click="changeTicketStatus('open')" class="text-sm md:inline-flex hidden justify-center rounded-md border border-transparent bg-primary px-4 py-1 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">{{ $t('Mark as open') }}</button>

                <Dropdown v-if="!displayContact">
                    <button type="button" class="inline-flex w-full justify-center items-center rounded-md text-sm font-medium text-black hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                        <span class="bg-slate-200 p-1 rounded-full cursor-pointer flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.75a.75.75 0 1 1 0-1.5a.75.75 0 0 1 0 1.5Zm0 6a.75.75 0 1 1 0-1.5a.75.75 0 0 1 0 1.5Zm0 6a.75.75 0 1 1 0-1.5a.75.75 0 0 1 0 1.5Z"/></svg>
                        </span>
                    </button>
                    <template #items>
                        <DropdownItemGroup>
                            <DropdownItem @click="isOpenModal = true;" as="button">{{ $t('Add notes') }}</DropdownItem>
                            <DropdownItem v-if="ticketState === 'open' && ticketingIsEnabled" @click="changeTicketStatus('closed')" as="button">{{ $t('Mark as closed') }}</DropdownItem>
                            <DropdownItem v-if="ticketState === 'closed' && ticketingIsEnabled" @click="changeTicketStatus('open')" as="button">{{ $t('Mark as open') }}</DropdownItem>
                            <DropdownItem @click="showAlert = true" as="button">{{ $t('Clear chat') }}</DropdownItem>
                        </DropdownItemGroup>
                    </template>
                </Dropdown>
                <div v-else @click="toggleView" class="bg-slate-200 p-2 rounded-lg text-sm cursor-pointer">
                    <span>{{ $t('Back') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div v-if="addon == 1" class="flex justify-between items-center bg-white border-b border-1 py-2 px-4 md:px-4">
        <div class="text-sm py-1 px-3 rounded-md flex items-center gap-x-2 w-[fit-content]" :class="form3.ai_assistant ? 'bg-green-50' : 'bg-red-50'">
            <span :class="form3.ai_assistant ? 'text-green-500' : 'text-red-500'">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24"><path fill="currentColor" d="M12 22q-2.075 0-3.9-.788t-3.175-2.137T2.788 15.9T2 12t.788-3.9t2.137-3.175T8.1 2.788T12 2t3.9.788t3.175 2.137T21.213 8.1T22 12t-.788 3.9t-2.137 3.175t-3.175 2.138T12 22"/></svg>
            </span>
            <span>{{ $t('AI Assistant') }}</span>
        </div>
        <div class="flex items-center gap-x-2">
            <span class="text-[13px]">{{ $t('Enable/disable AI assistant') }}</span>
            <div class="w-12 h-6 flex items-center bg-gray-300 rounded-full p-1 cursor-pointer" :class="{ 'bg-primary': form3.ai_assistant}" @click="submitForm3()">
                <div class="bg-white w-4 h-4 rounded-full shadow-md transform duration-300 ease-in-out" :class="{ 'translate-x-6': form3.ai_assistant }"></div>
            </div>
        </div>
    </div>

    <Modal :label="'Add Note'" :isOpen="isOpenModal">
        <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4">
            <form @submit.prevent="submitForm()" class="">
                <FormTextArea v-model="form2.notes" :error="form2.errors.note" :name="''" :class="'col-span-2'"/>
                <div class="mt-4 flex">
                    <button type="button" @click.self="isOpenModal = false" class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{ $t('Cancel') }}</button>
                    <button 
                        :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': form2.processing }]"
                        :disabled="form2.processing"
                    >
                        <svg v-if="form2.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                        <span v-else>{{ $t('Save') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </Modal>

    <AlertModal 
        v-model="showAlert" 
        :label="$t('Clear chat')" 
        :description="$t('Are you sure you want to delete this thread? You can\'t undo this action')" 
        @confirm="deleteThread" />
</template> -->

<!-- ========================================== NEW UI CODE ==================================== -->

<script setup>
import { ref, watchEffect, computed } from 'vue';
import { router, useForm, Link, usePage } from '@inertiajs/vue3'
import AlertModal from '@/Components/AlertModal.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import FormSelectCombo from '@/Components/FormSelectCombo.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import Modal from '@/Components/Modal.vue';
import { trans } from 'laravel-vue-i18n';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps(['contact', 'displayContactInfo', 'ticketingIsEnabled', 'ticket', 'addon']);

const accountUser = computed(() => usePage().props.auth.user);
const showAlert = ref(false);
const displayContact = ref(props.displayContactInfo);
const ticketState = ref(null);
const isOpenModal = ref(false);
const user = ref({
    'label': props.ticket?.user ? props.ticket?.user?.full_name : trans('Unassigned'),
    'value': props.ticket?.user ? props.ticket?.user?.id : 0,
});

watchEffect(() => {
    displayContact.value = props.displayContactInfo;
    ticketState.value = props.ticket?.status;
});

const emit = defineEmits(['toggleView', 'deleteThread', 'closeThread']);

const toggleView = () => {
    displayContact.value = !displayContact.value;
    emit('toggleView', displayContact.value);
}

const deleteThread = () => {
    router.visit('/chats/' + props.contact.uuid, {
        method: 'delete',
        onFinish: () => {
            showAlert.value = false
        }
    });
}

const form2 = useForm({
    'notes': null,
    'contact': null
});

const form3 = useForm({
    'ai_assistant': props.contact?.ai_assistance_enabled,
});

const changeTicketStatus = (value) => {
    router.put('/tickets/' + props.contact.uuid + '/update', {
        'status': value
    }, {})
}

const changeTicketAgent = () => {
    router.put('/tickets/' + props.contact.uuid + '/assign', {
        'id': user.value.value
    }, {})
}

function loadUsers(query, setOptions) {
    fetch("/team?search=" + query, {
        headers: { 'Accept': 'application/json' }
    })
        .then(response => response.json())
        .then(result => {
            setOptions(result.rows);
        })
        .catch(error => {
            console.error("Error fetching agents:", error);
        });
}

const submitForm = () => {
    form2.contact = props.contact.uuid;
    form2.post('/notes', {
        preserveState: false,
        onSuccess: () => {
            form2.reset();
            isOpenModal.value = false;
        }
    });
}

const submitForm3 = () => {
    form3.ai_assistant = !form3.ai_assistant;
    form3.post('/automation/contact/' + props.contact.uuid, {
        preserveState: true,
    });
}
</script>

<template>
    <!-- Main Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Contact Info -->
            <div class="flex items-center gap-4 flex-1">
                <Link href="/chats" class="sm:block md:hidden text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M19 11H7.14l3.63-4.36a1 1 0 1 0-1.54-1.28l-5 6a1.19 1.19 0 0 0-.09.15c0 .05 0 .08-.07.13A1 1 0 0 0 4 12a1 1 0 0 0 .07.36c0 .05 0 .08.07.13a1.19 1.19 0 0 0 .09.15l5 6A1 1 0 0 0 10 19a1 1 0 0 0 .64-.23a1 1 0 0 0 .13-1.41L7.14 13H19a1 1 0 0 0 0-2" />
                </svg>
                </Link>

                <div @click="toggleView" class="cursor-pointer">
                    <img v-if="contact.avatar" class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-100"
                        :src="contact.avatar">
                    <div v-else
                        class="w-12 h-12 rounded-full bg-gradient-to-br from-[#ff5100] to-orange-400 flex items-center justify-center text-white font-semibold text-lg">
                        {{ contact.full_name.substring(0, 1) }}
                    </div>
                </div>

                <div @click="toggleView" class="flex-1 min-w-0 max-w-max">
                    <h2
                        class="text-lg font-semibold text-gray-900 cursor-pointer hover:text-[#ff5100] transition-colors">
                        {{ contact.full_name }}
                    </h2>
                    <p class="text-sm text-gray-500 cursor-pointer">{{
                        contact.formatted_phone_number }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <FormSelectCombo v-if="ticketingIsEnabled && accountUser.teams[0]['role'] != 'agent'" v-model="user"
                    :name="''" :loadOptions="loadUsers" :class="'col-span-1 md:block hidden'"
                    :placeholder="'Select Agent'" @update:modelValue="changeTicketAgent()" />
                <button v-if="ticketState === 'open' && ticketingIsEnabled" @click="changeTicketStatus('closed')"
                    class="hidden md:inline-flex items-center gap-2 px-4 py-2 bg-[#ff5100] hover:bg-[#e64900] text-white rounded-xl text-sm font-medium transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                    </svg>
                    {{ $t('Mark as closed') }}
                </button>

                <button v-if="ticketState === 'closed' && ticketingIsEnabled" @click="changeTicketStatus('open')"
                    class="hidden md:inline-flex items-center gap-2 px-4 py-2 bg-[#ff5100] hover:bg-[#e64900] text-white rounded-xl text-sm font-medium transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                    {{ $t('Mark as open') }}
                </button>

                <Dropdown v-if="!displayContact">
                    <button type="button" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M12 6.75a.75.75 0 1 1 0-1.5a.75.75 0 0 1 0 1.5Zm0 6a.75.75 0 1 1 0-1.5a.75.75 0 0 1 0 1.5Zm0 6a.75.75 0 1 1 0-1.5a.75.75 0 0 1 0 1.5Z" />
                        </svg>
                    </button>
                    <template #items>
                        <DropdownItemGroup>
                            <DropdownItem @click="isOpenModal = true;" as="button">{{ $t('Add notes') }}</DropdownItem>
                            <DropdownItem v-if="ticketState === 'open' && ticketingIsEnabled"
                                @click="changeTicketStatus('closed')" as="button">{{ $t('Mark as closed') }}
                            </DropdownItem>
                            <DropdownItem v-if="ticketState === 'closed' && ticketingIsEnabled"
                                @click="changeTicketStatus('open')" as="button">{{ $t('Mark as open') }}</DropdownItem>
                            <DropdownItem @click="showAlert = true" as="button">{{ $t('Clear chat') }}</DropdownItem>
                        </DropdownItemGroup>
                    </template>
                </Dropdown>

                <button v-else @click="toggleView"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
                    <ArrowLeft class="w-4 h-4" />
                    <span>{{ $t('Back') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- AI Assistant Toggle -->
    <div v-if="addon == 1" class="bg-white border-b border-gray-200 px-6 py-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full" :class="form3.ai_assistant ? 'bg-green-500' : 'bg-red-500'"></div>
                <span class="text-sm font-medium text-gray-700">{{ $t('AI Assistant') }}</span>
                <span class="text-xs px-2 py-1 rounded-full"
                    :class="form3.ai_assistant ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                    {{ form3.ai_assistant ? $t('Active') : $t('Inactive') }}
                </span>
            </div>

            <button @click="submitForm3()"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                :class="form3.ai_assistant ? 'bg-[#ff5100]' : 'bg-gray-300'">
                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                    :class="form3.ai_assistant ? 'translate-x-6' : 'translate-x-1'">
                </span>
            </button>
        </div>
    </div>

    <!-- Add Note Modal -->
    <Modal :label="$t('Add Note')" :isOpen="isOpenModal">
        <div class="mt-5">
            <form @submit.prevent="submitForm()">
                <FormTextArea v-model="form2.notes" :error="form2.errors.note" :name="''" :class="'col-span-2'" />
                <div class="mt-6 flex gap-3">
                    <button type="button" @click.self="isOpenModal = false"
                        class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all">
                        {{ $t('Cancel') }}
                    </button>
                    <button type="submit" :disabled="form2.processing"
                        :class="['flex-1 px-4 py-3 rounded-xl text-sm font-medium text-white transition-all flex items-center justify-center gap-2',
                            form2.processing ? 'bg-[#ff5100]/70 cursor-not-allowed' : 'bg-[#ff5100] hover:bg-[#e64900]']">
                        <svg v-if="form2.processing" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" class="animate-spin">
                            <path fill="currentColor"
                                d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                opacity=".5" />
                            <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                    repeatCount="indefinite" to="360 12 12" type="rotate" />
                            </path>
                        </svg>
                        <span>{{ $t('Save') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </Modal>

    <AlertModal v-model="showAlert" :label="$t('Clear chat')"
        :description="$t('Are you sure you want to delete this thread? You can\'t undo this action')"
        @confirm="deleteThread" />
</template>