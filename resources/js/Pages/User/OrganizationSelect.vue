<!-- <template>
    <div :class="rtlClass">
        <div class="flex h-screen w-full tracking-[0.3px] bg-gray-300/10">
            <div class="absolute top-4 right-4">
                <Link href="/logout"
                    class="hover:bg-[#F6F7F9] hover:rounded-md h-[fit-content] w-[fit-content] p-2 flex gap-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="m12 15l3-3m0 0l-3-3m3 3H4m5-4.751V7.2c0-1.12 0-1.68.218-2.108c.192-.377.497-.682.874-.874C10.52 4 11.08 4 12.2 4h4.6c1.12 0 1.68 0 2.107.218c.377.192.683.497.875.874c.218.427.218.987.218 2.105v9.607c0 1.118 0 1.677-.218 2.104a2.002 2.002 0 0 1-.875.874c-.427.218-.986.218-2.104.218h-4.606c-1.118 0-1.678 0-2.105-.218a2 2 0 0 1-.874-.874C9 18.48 9 17.92 9 16.8v-.05" />
                </svg>
                <span class="font-light">{{ $t('Logout') }}</span>
                </Link>
            </div>
            <div class="min-h-screen flex flex-col w-full min-w-0 items-center justify-center space-x-4">
                <div class="bg-white rounded-xl p-5 border">
                    <h4 class="font-regular text-xl mb-4 text-center">{{ $t('Select organization') }}</h4>

                    <div class="min-w-[300px] border-t pt-4 space-y-4">
                        <div v-for="(item, index) in props.organizations" :key="index"
                            @click="selectOrganization(item.organization.uuid)"
                            class="flex space-x-8 bg-white rounded-md px-4 py-2 justify-between items-center w-full hover:shadow-md cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <span class="bg-slate-200 w-12 h-12 rounded-full flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <g fill="none" fill-rule="evenodd">
                                            <path
                                                d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z" />
                                            <path fill="currentColor"
                                                d="M17 3.722v5.497l2.864.716A1.5 1.5 0 0 1 21 11.39V19a1 1 0 1 1 0 2H3a1 1 0 1 1 0-2v-7.69a1.5 1.5 0 0 1 .83-1.343L7 8.382V6.347a1.5 1.5 0 0 1 .973-1.405l7-2.625A1.5 1.5 0 0 1 17 3.722Zm-2 .721l-6 2.25V19h6V4.443Zm2 6.838V19h2v-7.22l-2-.5Zm-10-.663l-2 1V19h2v-8.382Z" />
                                        </g>
                                    </svg>
                                </span>
                                <div>
                                    <h2>{{ item.organization.name }}</h2>
                                </div>
                            </div>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20">
                                    <path fill="currentColor" fill-rule="evenodd"
                                        d="M5 2.643v14.765c.092.32.299.511.619.572c.32.061.633-.024.94-.255l8.107-6.993A.944.944 0 0 0 15 10a.94.94 0 0 0-.334-.73L6.58 2.295c-.232-.197-.639-.383-1.061-.253c-.282.087-.455.287-.519.6" />
                                </svg>
                            </span>
                        </div>
                        <div @click="isOpenModal = true"
                            class="flex space-x-8 bg-white border rounded-md px-4 py-2 justify-between items-center w-full hover:bg-slate-100 cursor-pointer">
                            <div class="w-full">
                                <h2 class="font-light text-center text-sm">Create Organization</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <OrganizationModal v-model:modelValue="isOpenModal" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import OrganizationModal from '@/Components/OrganizationModal.vue';
import { useRtl } from '../../Composables/useRtl';

const { rtlClass } = useRtl();

const props = defineProps({ organizations: Object });

const isOpenModal = ref(false);

const form = useForm({
    uuid: null,
})

const selectOrganization = (uuid) => {
    form.uuid = uuid;

    submitForm();
}

const submitForm = async () => {
    form.post('/select-organization', {
        preserveScroll: true,
    })
};
</script> -->

<!-- ================================ NEW CODE ================================ -->

<template>
    <div :class="rtlClass" class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
        <!-- Logout: top-right on lg, hidden on small (moved to header) -->
        <div class="absolute top-4 right-4 hidden lg:block">
            <Link href="/logout"
                class="inline-flex items-center gap-2 rounded-md px-3 py-2 hover:bg-white/60 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-700" viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m12 15l3-3m0 0l-3-3m3 3H4m5-4.751V7.2c0-1.12 0-1.68.218-2.108c.192-.377.497-.682.874-.874C10.52 4 11.08 4 12.2 4h4.6c1.12 0 1.68 0 2.107.218c.377.192.683.497.875.874.218.427.218.987.218 2.105v9.607c0 1.118 0 1.677-.218 2.104a2.002 2.002 0 0 1-.875.874c-.427.218-.986.218-2.104.218h-4.606c-1.118 0-1.678 0-2.105-.218a2 2 0 0 1-.874-.874C9 18.48 9 17.92 9 16.8v-.05" />
            </svg>
            <span class="text-sm text-slate-700 font-medium">{{ $t('Logout') }}</span>
            </Link>
        </div>

        <div class="w-full max-w-2xl">
            <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-4">
                <!-- Header: includes Logout on mobile -->
                <header class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">{{ $t('Select Organization') }}</h2>

                    <div class="flex items-center gap-3">
                        <div class="text-sm text-slate-500 hidden sm:block">
                            {{ props.organizations?.length ?? 0 }} {{ $t('organizations') }}
                        </div>

                        <!-- Mobile logout shown here (lg:hidden) -->
                        <div class="block lg:hidden">
                            <Link href="/logout"
                                class="inline-flex items-center gap-2 rounded-md px-2 py-1 hover:bg-slate-50 transition text-sm text-slate-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m12 15l3-3m0 0l-3-3m3 3H4m5-4.751V7.2c0-1.12 0-1.68.218-2.108c.192-.377.497-.682.874-.874C10.52 4 11.08 4 12.2 4h4.6c1.12 0 1.68 0 2.107.218c.377.192.683.497.875.874.218.427.218.987.218 2.105v9.607c0 1.118 0 1.677-.218 2.104a2 2 0 0 1-.875.874c-.427.218-.986.218-2.104.218h-4.606c-1.118 0-1.678 0-2.105-.218a2 2 0 0 1-.874-.874C9 18.48 9 17.92 9 16.8v-.05" />
                            </svg>
                            </Link>
                        </div>
                    </div>
                </header>

                <!-- CARD GRID -->
                <section class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" role="grid" @keydown="onKeydown">
                    <article v-for="(item, idx) in props.organizations" :key="item.organization.uuid" role="button"
                        :aria-pressed="activeIndex === idx ? 'true' : 'false'" :tabindex="0"
                        :ref="el => (cardRefs[idx] = el)" @click="activateCard(idx)" @focus="focusedIndex = idx"
                        @mouseenter="focusedIndex = idx"
                        class="relative bg-white border rounded-xl p-4 shadow-sm hover:shadow-md transition transform hover:-translate-y-0.5 focus:outline-none"
                        :class="activeIndex === idx ? 'ring-2 ring-primary/30 border-primary/10' : 'border-transparent'">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-lg grid place-items-center bg-slate-50 text-primary shrink-0">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M17 3.722v5.497l2.864.716A1.5 1.5 0 0 1 21 11.39V19a1 1 0 1 1 0 2H3a1 1 0 1 1 0-2v-7.69a1.5 1.5 0 0 1 .83-1.343L7 8.382V6.347a1.5 1.5 0 0 1 .973-1.405l7-2.625A1.5 1.5 0 0 1 17 3.722z" />
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm font-semibold text-slate-900 truncate">{{ item.organization.name
                                    }}</div>
                                    <div class="text-xs text-slate-400">{{ item.organization?.members_count ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Open button bottom-right of card -->
                        <div class="absolute right-4 bottom-4">
                            <button
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium transition"
                                :class="loadingMap[item.organization.uuid] ? 'bg-primary text-white' : 'bg-white border border-slate-200 hover:bg-slate-50 text-slate-700'"
                                @click.stop="openOrganization(item.organization.uuid)"
                                :disabled="loadingMap[item.organization.uuid]">
                                <svg v-if="!loadingMap[item.organization.uuid]" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="w-3 h-3 lucide lucide-external-link-icon lucide-external-link">
                                    <path d="M15 3h6v6" />
                                    <path d="M10 14 21 3" />
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                </svg>
                                <svg v-else class="w-3 h-3 animate-spin" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M12 2A10 10 0 1 0 22 12" />
                                </svg>
                                <span class="whitespace-nowrap text-[13px]">{{ loadingMap[item.organization.uuid] ?
                                    $t('Opening') : $t('Open') }}</span>
                            </button>
                        </div>
                    </article>
                </section>

                <!-- Create organization button -->
                <div class="w-full mt-6 flex items-center justify-end sm:justify-end">
                    <button
                        class="flex justify-center items-center gap-2 px-3 py-2 rounded-md border border-slate-200 text-sm text-slate-700 hover:bg-slate-50 transition"
                        @click="openOrgModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M8 12h8" />
                            <path d="M12 8v8" />
                        </svg>
                        {{ $t('Create Organization') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- OrganizationModal unchanged -->
        <OrganizationModal v-model:modelValue="isOpenModal" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import OrganizationModal from '@/Components/OrganizationModal.vue';
import { useRtl } from '../../Composables/useRtl';

const { rtlClass } = useRtl();
const props = defineProps({ organizations: Object });

/* refs for card elements */
const cardRefs = ref([]);

/* state */
const isOpenModal = ref(false);
const form = useForm({ uuid: null });
const activeIndex = ref(0);
const focusedIndex = ref(-1);

/* loading per-org map */
const loadingMap = ref({}); // { [uuid]: boolean }

/* manage current columns for keyboard nav (1/2/3) */
const cols = ref(1);
const updateCols = () => {
    const w = window.innerWidth;
    if (w < 640) cols.value = 1;
    else if (w < 1024) cols.value = 2;
    else cols.value = 3;
};

/* compute total */
const total = computed(() => props.organizations?.length ?? 0);

/* activate card (select visually) */
const activateCard = (idx) => {
    activeIndex.value = idx;
    focusedIndex.value = idx;
    // focus DOM for accessibility
    nextTick(() => {
        cardRefs.value[idx]?.focus?.();
    });
};

/* open organization: sets loading flag and posts */
const openOrganization = (uuid) => {
    if (loadingMap.value[uuid]) return;

    loadingMap.value = { ...loadingMap.value, [uuid]: true };

    form.uuid = uuid;
    form.post('/select-organization', {
        preserveScroll: true,
        onFinish: () => {
            setTimeout(() => {
                loadingMap.value = { ...loadingMap.value, [uuid]: false };
            }, 600);
        }
    });
};

/* keyboard navigation for a grid: Left/Right/Up/Down/Home/End/Enter */
const onKeydown = (e) => {
    if (total.value === 0) return;

    if (e.key === 'ArrowRight') {
        activeIndex.value = (activeIndex.value + 1) % total.value;
        focusActive();
        e.preventDefault();
    } else if (e.key === 'ArrowLeft') {
        activeIndex.value = (activeIndex.value - 1 + total.value) % total.value;
        focusActive();
        e.preventDefault();
    } else if (e.key === 'ArrowDown') {
        activeIndex.value = (activeIndex.value + cols.value) % total.value;
        focusActive();
        e.preventDefault();
    } else if (e.key === 'ArrowUp') {
        activeIndex.value = (activeIndex.value - cols.value + total.value) % total.value;
        focusActive();
        e.preventDefault();
    } else if (e.key === 'Home') {
        activeIndex.value = 0;
        focusActive();
        e.preventDefault();
    } else if (e.key === 'End') {
        activeIndex.value = total.value - 1;
        focusActive();
        e.preventDefault();
    } else if (e.key === 'Enter' || e.key === ' ') {
        const uuid = props.organizations[activeIndex.value].organization.uuid;
        openOrganization(uuid);
        e.preventDefault();
    }
};

const focusActive = () => {
    focusedIndex.value = activeIndex.value;
    nextTick(() => {
        cardRefs.value[activeIndex.value]?.focus?.();
        // ensure element visible (helpful on small screens)
        cardRefs.value[activeIndex.value]?.scrollIntoView?.({
            block: 'nearest',
            // behavior: 'smooth'
        });
    });
};

const openOrgModal = () => {
    isOpenModal.value = true;
};

onMounted(() => {
    updateCols();
    window.addEventListener('resize', updateCols);
    nextTick(() => {
        // populate refs array
        cardRefs.value = cardRefs.value.slice(0, total.value);
        // initial focus on first
        if (total.value > 0) {
            nextTick(() => {
                cardRefs.value[0]?.focus?.();
            });
        }
    });
});

watch(() => props.organizations, () => {
    if (activeIndex.value >= total.value) activeIndex.value = Math.max(0, total.value - 1);
    nextTick(() => {
        cardRefs.value = cardRefs.value.slice(0, total.value);
    });
});

</script>