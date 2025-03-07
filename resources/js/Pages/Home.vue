<script setup>
import { ref } from 'vue';

const count = ref(0);

const increment = () => {
  count.value++;
};

const decrement = () => {
  if (count.value > 0) count.value--;
};
</script>

<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <h1 class="text-3xl font-bold text-white bg-red-500 px-6 py-3 rounded-lg shadow-lg">
      Vue 3 & Inertia.js Berjalan!
    </h1>

    <!-- Counter Box -->
    <div class="mt-6 bg-white p-6 rounded-lg shadow-lg text-center">
      <p class="text-xl font-semibold">Counter:</p>
      <p class="text-4xl font-bold text-blue-600 my-3">{{ count }}</p>

      <!-- Buttons -->
      <div class="flex gap-4">
        <button 
          @click="decrement"
          class="px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 transition">
          −
        </button>
        <button 
          @click="increment"
          class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
          +
        </button>
      </div>
    </div>
  </div>
</template>
