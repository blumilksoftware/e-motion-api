<script setup>
import PrimaryButton from '@/Shared/Components/PrimaryButton.vue'
import SecondaryButton from '@/Shared/Components/SecondaryButton.vue'
import Footer from './Footer.vue'
import { __ } from '@/translate'

const emit = defineEmits(['try-it-out', 'create-account'])
</script>

<template>
  <div class="flex h-full flex-col items-start justify-between bg-blumilk-25 px-6 md:px-10 lg:px-14">
    <div class="hidden md:flex md:w-7/12 lg:w-3/4 xl:w-7/12">
      <img class="h-auto max-w-full" src="@/assets/undraw_scooter.svg" alt="Man riding an e-scooter">
    </div>

    <div class="mt-16 md:mt-0 lg:pb-16">
      <h1 class="text-4xl font-bold tracking-tight text-gray-800 sm:text-5xl">
        {{ __('Looking for an e‑scooter?') }}
      </h1>
      <p class="mx-0 w-full items-start pt-10 text-sm leading-7 text-gray-600 sm:w-2/3 sm:text-base lg:mx-auto lg:w-full lg:text-lg">
        {{ __('advert1') }}
        <br><br>
        <strong>{{ __('advert2') }}</strong>
      </p>
    </div>

    <div
      class="my-4 flex w-full flex-wrap items-center justify-center space-y-5 sm:hidden sm:justify-between sm:space-y-0 md:flex"
    >
      <PrimaryButton class="h-fit w-11/12 px-2 py-5 sm:w-5/12" @click="emit('try-it-out')">
        {{ __('Try it out') }}
      </PrimaryButton>

      <SecondaryButton class="h-fit w-11/12 px-2 py-5 sm:w-5/12" @click="emit('create-account')">
        {{ __('Create account') }}
      </SecondaryButton>
    </div>

    <div class="hidden w-full items-center space-x-12 sm:flex md:hidden">
      <div class="flex w-2/3 md:h-1/2 lg:h-1/3">
        <img class="max-h-full" src="@/assets/undraw_scooter.svg" alt="Man riding an e-scooter">
      </div>

      <div class="flex w-1/3 flex-col space-y-8">
        <PrimaryButton class="h-fit w-full px-2 py-5" @click="emit('try-it-out')">
          Try it out
        </PrimaryButton>

        <SecondaryButton class="h-fit w-full px-2 py-5" @click="emit('create-account')">
          {{ __('Create account') }}
        </SecondaryButton>
      </div>
    </div>
    <div class="flex w-full justify-center">
      <Footer />
    </div>
  </div>
</template>
