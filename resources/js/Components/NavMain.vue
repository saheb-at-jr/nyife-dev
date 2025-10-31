<!-- <script setup lang="ts">
import type { LucideIcon } from "lucide-vue-next"
import { ChevronRight } from "lucide-vue-next"
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '../Components/ui/collapsible'
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem,
} from '../Components/ui/sidebar'

defineProps<{
  items: {
    title: string
    url: string
    icon?: LucideIcon
    items?: {
      title: string
      url: string
    }[]
  }[]
}>()
</script>

<template>
  <SidebarGroup>
    <SidebarGroupLabel>Platform</SidebarGroupLabel>
    <SidebarMenu>
      <Collapsible v-for="item in items" :key="item.title" as-child :default-open="true" class="group/collapsible">
        <SidebarMenuItem>
          <CollapsibleTrigger as-child>
            <SidebarMenuButton :tooltip="item.title">
              <component :is="item.icon" v-if="item.icon" />
              <span>{{ item.title }}</span>
              <ChevronRight
                class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
            </SidebarMenuButton>
          </CollapsibleTrigger>
          <CollapsibleContent>
            <SidebarMenuSub>
              <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                <SidebarMenuSubButton as-child>
                  <a :href="subItem.url">
                    <span>{{ subItem.title }}</span>
                  </a>
                </SidebarMenuSubButton>
              </SidebarMenuSubItem>
            </SidebarMenuSub>
          </CollapsibleContent>
        </SidebarMenuItem>
      </Collapsible>
    </SidebarMenu>
  </SidebarGroup>
</template> -->


<!-- ============================= NEW CODE =============================== -->

<script setup lang="ts">
// import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import type { LucideIcon } from 'lucide-vue-next'
import { ChevronRight } from 'lucide-vue-next'

import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '../Components/ui/collapsible'

import {
  SidebarGroup,
  // SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem,
} from '../Components/ui/sidebar'

/**
 * Props typing
 */
const props = defineProps<{
  items: {
    title: string
    url: string
    icon?: LucideIcon
    items?: {
      title: string
      url: string
    }[]
  }[]
}>()

const page = usePage()

/**
 * Helpers
 */
function normalizeUrl(u?: string) {
  if (!u) return ''
  // ensure trailing slash handling if you need exact startsWith logic
  return u
}

/** returns true when a URL should be considered active */
function urlMatches(current: string, target: string) {
  if (!target) return false
  // Exact match OR startsWith to cover nested routes like /dashboard/stats
  return current === target || current.startsWith(target)
}

/** parent active: either parent url matches OR any child matches */
function isParentActive(item: typeof props.items[number]) {
  const current = (page as any).url as string
  if (urlMatches(current, normalizeUrl(item.url))) return true
  if (item.items && item.items.length) {
    return item.items.some((sub) => urlMatches(current, normalizeUrl(sub.url)))
  }
  return false
}

/** subitem active */
function isSubActive(sub: { title: string; url: string }) {
  const current = (page as any).url as string
  return urlMatches(current, normalizeUrl(sub.url))
}
</script>

<template>
  <SidebarGroup>
    <!-- <SidebarGroupLabel>Platform</SidebarGroupLabel> -->
    <SidebarMenu>
      <template v-for="item in props.items" :key="item.title">
        <!-- If no sub-items: render a single clickable menu item -->
        <SidebarMenuItem v-if="!item.items || item.items.length === 0">
          <SidebarMenuButton :tooltip="item.title"
            :class="isParentActive(item) ? 'bg-slate-50 text-primary border-l-4 border-primary rounded-none bg-primary/5 hover:text-primary hover:bg-primary/5' : ''">
            <Link :href="item.url" class="flex items-center w-full space-x-3 p-2 rounded-md">
            <component v-if="item.icon" :is="item.icon" class="w-5 h-5" />
            <span class="truncate">{{ $t(item.title) }}</span>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>

        <!-- If has sub-items: render a collapsible group -->
        <Collapsible v-else as-child :default-open="isParentActive(item)" class="group/collapsible">
          <SidebarMenuItem>
            <CollapsibleTrigger as-child>
              <SidebarMenuButton :tooltip="item.title"
                :class="isParentActive(item) ? 'bg-slate-50 text-primary border-l-4 border-primary rounded-none bg-primary/5 hover:text-primary hover:bg-primary/5' : ''">
                <div class="flex items-center space-x-3">
                  <component v-if="item.icon" :is="item.icon" class="w-5 h-5" />
                  <span class="truncate">{{ $t(item.title) }}</span>
                </div>

                <!-- chevron that rotates when open -->
                <ChevronRight
                  class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
              </SidebarMenuButton>
            </CollapsibleTrigger>

            <CollapsibleContent>
              <SidebarMenuSub>
                <SidebarMenuSubItem v-for="sub in item.items" :key="sub.title">
                  <SidebarMenuSubButton as-child>
                    <!-- use Link to keep client navigation -->
                    <Link :href="sub.url" class="flex items-center w-full p-2 rounded-md"
                      :class="isSubActive(sub) ? 'bg-slate-50 text-primary border-l-4 border-primary rounded-none bg-primary/5 hover:text-primary hover:bg-primary/5' : ''">
                    <span class="truncate">{{ $t(sub.title) }}</span>
                    </Link>
                  </SidebarMenuSubButton>
                </SidebarMenuSubItem>
              </SidebarMenuSub>
            </CollapsibleContent>
          </SidebarMenuItem>
        </Collapsible>
      </template>
    </SidebarMenu>
  </SidebarGroup>
</template>
