<script setup>
import { computed, ref } from 'vue'
import { ChevronDownIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { onClickOutside } from '@vueuse/core'
import { __ } from '@/translate'

const options = { hour: 'numeric', minute: 'numeric', second: 'numeric' }

const props = defineProps({
  info: Object,
  codes: Object,
  providers: Object,
})

const status = computed(() => {
  const importDetails = props.info.import_info_details

  if (importDetails.length === 0) {
    return 'Success'
  } else if (importDetails.some(item => item.code === '400' || item.code === '204')) {
    return 'Error'
  } else {
    return 'Warning'
  }
})

const isImportDialogOpened = ref(false)
const importDialog = ref(null)
onClickOutside(importDialog, () => (isImportDialogOpened.value = false))

function toggleImportDialog() {
  if (status.value !== 'Success') {
    isImportDialogOpened.value = !isImportDialogOpened.value
  }
}

</script>

<template>
  <tr :class="(status === 'Success') || (info.status !== 'finished') ? '' : 'hover:bg-gray-100' "
      class="border-t"
  >
    <td class="table-cell break-all py-3.5 pl-2 text-xs capitalize text-gray-500 sm:pl-6">
      {{ info.who_runs_it }}
    </td>
    <td class="table-cell break-all px-4 py-3.5 text-xs text-gray-500">
      {{ new Date(info.created_at).toLocaleDateString("pl-PL", options) }}
    </td>

    <td v-if="info.status === 'finished'"
        :class="[
          status === 'Success' ? 'text-emerald-400' :
          status === 'Error' ? 'text-red-600' : 'text-orange-400']"
    >
      <div class="flex">
        <div class="w-fit rounded-full p-1">
          <div class="size-1.5 rounded-full bg-current" />
        </div>
        <div class="text-xs font-medium">
          {{ __(status) }}
        </div>
      </div>
    </td>
    <td v-else
        class="table-cell items-center py-3.5 text-sm text-gray-500"
    >
      <div class="flex">
        <div class="flex w-fit animate-pulse rounded-full p-1">
          <div class="size-1.5 rounded-full bg-current" />
        </div>
        <div class="text-xs font-medium capitalize">
          {{ __(info.status) }}
        </div>
      </div>
    </td>
    <td v-if="status !== 'Success' && info.status === 'finished'" class="table-cell cursor-pointer pl-1 text-gray-400 hover:text-black" @click="toggleImportDialog">
      <ChevronDownIcon class="h-5 w-full" />
    </td>
  </tr>

  <div v-if="isImportDialogOpened" class="flex flex-col">
    <div class="fixed inset-0 z-10 flex items-center bg-black/50 py-8">
      <div ref="importDialog" class="scrollbar mx-auto h-fit max-h-full w-11/12 overflow-y-auto rounded-lg bg-white pb-6 sm:w-5/6 md:w-3/4 lg:w-1/2 xl:w-1/3">
        <div class="flex w-full justify-end">
          <button class="px-4 pt-4" @click="toggleImportDialog">
            <XMarkIcon class="size-6" />
          </button>
        </div>

        <div class="flex flex-col">
          <div class="size-full flex-col px-6">
            <div class="mb-4 flex flex-col">
              <p class="text-xs capitalize text-gray-400">
                {{ info.who_runs_it }}
              </p>
              <p class="text-sm font-bold text-gray-500">
                {{ new Date(info.created_at).toLocaleDateString("pl-PL", options) }}
              </p>
            </div>

            <div v-for="detail in info.import_info_details" :key="detail.id"
                 :class="detail.code === 400 ? 'border-red-600 bg-red-100' : 'border-orange-500 bg-orange-100'"
                 class="mb-4 flex flex-col justify-center rounded border p-2 font-light"
            >
              <div v-for="provider in providers" :key="provider.name">
                <div v-if="detail.provider_name === provider.name">
                  <div
                    :style="{'background-color': provider.color}"
                    class="mr-2 flex h-9 w-fit items-center justify-center rounded p-1"
                  >
                    <img :src="`/images/providers/${provider.name.toLowerCase()}.png`" alt="" class="w-12">
                  </div>
                </div>
              </div>

              <div v-for="code in codes" :key="code.number">
                <div v-if="detail.code === code.number" class="mt-1 text-sm text-gray-800">
                  <p class="font-normal">
                    {{ __(code.description) }}
                  </p>
                  <InertiaLink v-if="detail.code === 419" href="/admin/cities?order=empty-coordinates" class="font-medium">
                    {{ __('Check list of cities with no coordinates.') }}
                  </InertiaLink>

                  <InertiaLink v-if="detail.code === 420" href="/admin/cities" class="font-medium">
                    {{ __('Check list of cities with no country assigned') }}
                  </InertiaLink>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
