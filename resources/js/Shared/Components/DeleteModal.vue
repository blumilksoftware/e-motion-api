<script setup>
import { ref, computed } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ExclamationTriangleIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { __ } from '@/translate'
import { onClickOutside } from '@vueuse/core'

const emits = defineEmits(['close', 'delete'])

const closeModal = () => emits('close')
const confirmDelete = () => emits('delete')
const open = ref(true)

const props = defineProps({
  name: {
    type: String,
    required: false,
  },
  type: {
    type: String,
    required: true,
  },
})

const formattedType = computed(() => {
  return props.type.charAt(0).toLowerCase() + props.type.slice(1)
})

const renderHeader = () => {
  let translationKey = ''
  translationKey = __('Delete ' + formattedType.value) + ' :name'

  return (__(translationKey, { name: props.name })).trim() + '?'
}

const renderText = () => {
  let translationKey = ''
  translationKey = 'This operation cannot be undone.'


  return __(translationKey)
}

const dialogRef = ref(null)
onClickOutside(dialogRef, () => closeModal())

</script>

<template>
  <TransitionRoot as="template" :show="open">
    <Dialog ref="dialog" as="div" class="relative z-10">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                       leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/50 transition-opacity" />
      </TransitionChild>
      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild as="template" enter="ease-out duration-300"
                           enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                           enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                           leave-from="opacity-100 translate-y-0 sm:scale-100"
                           leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel
              class="relative overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
            >
              <button class="absolute right-2 top-2 text-gray-400 hover:text-gray-600" @click="closeModal">
                <XMarkIcon class="size-5" aria-hidden="true" />
              </button>
              <div ref="dialogRef">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                  <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                      <ExclamationTriangleIcon class="size-6 text-red-600" aria-hidden="true" />
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                      <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">
                        {{ renderHeader() }}
                      </DialogTitle>
                      <div class="mt-2">
                        <p class="text-sm text-gray-500">
                          {{ renderText() }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                  <button type="button"
                          class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                          @click.stop="confirmDelete"
                  >
                    {{ __('Delete') }}
                  </button>
                  <button ref="cancelButtonRef" type="button"
                          class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                          @click="closeModal"
                  >
                    {{ __('Cancel') }}
                  </button>
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
