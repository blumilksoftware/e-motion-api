<script setup>
import { Dialog, DialogPanel } from '@headlessui/vue'
import { ClipboardIcon, FlagIcon, MapPinIcon, PlayCircleIcon, TruckIcon } from '@heroicons/vue/24/solid'
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'
import LanguageSwitch from '@/Shared/Components/LanguageSwitch.vue'
import { __ } from '@/translate'

const navigation = [
  { name: 'Dashboard', href: '/admin/dashboard', icon: ClipboardIcon },
  { name: 'Importers', href: '/admin/importers', icon: PlayCircleIcon },
  { name: 'Providers', href: '/admin/providers', icon: TruckIcon },
  { name: 'Countries', href: '/admin/countries', icon: FlagIcon },
  { name: 'Cities', href: '/admin/cities', icon: MapPinIcon },
]

const isMobileMenuOpened = ref(false)

function toggleMobileMenu() {
  isMobileMenuOpened.value = !isMobileMenuOpened.value
}
</script>

<template>
  <div
    class="fixed z-10 flex w-full border-blumilk-50 bg-blumilk-25 shadow md:h-full md:w-1/3 md:border-r lg:w-1/4 xl:w-1/6"
  >
    <div class="flex w-full justify-between md:flex-col md:justify-normal">
      <InertiaLink href="/" class="mt-3 flex shrink-0 items-center pb-3 pl-6 pr-2">
        <img class="h-10" src="@/assets/scooter.png" alt="escooter logo">
        <span class="ml-3 hidden text-2xl font-semibold text-gray-800 md:flex">e&#8209;scooters</span>
      </InertiaLink>

      <div class="mr-3.5 flex sm:hidden">
        <button type="button" class="inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                @click="toggleMobileMenu"
        >
          <span class="sr-only">{{ __('Open menu') }}</span>
          <Bars3Icon class="size-6" aria-hidden="true" />
        </button>
      </div>
      <Dialog v-if="isMobileMenuOpened" as="div" class="z-30 lg:hidden" :open="isMobileMenuOpened"
              @close="toggleMobileMenu"
      >
        <div class="fixed inset-0 z-30" />
        <DialogPanel
          class="fixed inset-y-0 right-0 z-30 w-full overflow-y-auto border-b-2 bg-white px-6 py-3 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10"
        >
          <div class="flex items-center justify-between sm:justify-end">
            <InertiaLink href="/">
              <img class="h-10 sm:hidden" src="@/assets/scooter.png" alt="escooter logo">
            </InertiaLink>
            <button type="button" class="-m-2.5 rounded-md px-2.5 text-gray-700 sm:pt-4"
                    @click="toggleMobileMenu"
            >
              <span class="sr-only">{{ __('Close menu') }}</span>
              <XMarkIcon class="size-6" aria-hidden="true" />
            </button>
          </div>
          <div class="mt-6 flow-root">
            <div class="-my-6 divide-y divide-gray-500/10">
              <div class="py-6">
                <InertiaLink v-for="item in navigation" :key="item.name"
                             :class="{'bg-blumilk-50': $page.url.startsWith(item.href)}" :href="item.href"
                             class="-mx-3 my-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-800 hover:bg-blumilk-25"
                >
                  {{ __(item.name) }}
                </InertiaLink>
              </div>
            </div>
          </div>
          <div class="mx-auto flex items-center pt-6">
            <LanguageSwitch class="text-2xl" />
          </div>
        </DialogPanel>
      </Dialog>

      <ul class="hidden h-full items-center text-sm font-medium text-gray-800 sm:flex md:mt-12 md:flex-col md:items-stretch md:space-y-2">
        <InertiaLink v-for="item in navigation" :key="item.name" :href="item.href" class="flex h-full md:h-fit">
          <div :class="{'bg-blumilk-50': $page.url.startsWith(item.href)}"
               class="mx-auto flex w-11/12 items-center bg-blumilk-25 px-6 hover:bg-blumilk-50 md:rounded-lg md:px-2 md:py-3"
          >
            <component :is="item.icon" class="size-7" />
            <span class="ml-3 hidden md:flex"> {{ __(item.name) }} </span>
          </div>
        </InertiaLink>
        <li class="flex px-5 md:pt-6">
          <LanguageSwitch />
        </li>
      </ul>
    </div>
  </div>
</template>
