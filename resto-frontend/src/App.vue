<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const menus = ref([])
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/menus')
    menus.value = res.data
  } catch (err) {
    error.value = 'Gagal mengambil data dari server'
    console.error(err)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div style="padding: 30px; font-family: Arial;">
    <h1>🍽 Smart Restaurant</h1>
    <h2>Daftar Menu</h2>

    <div v-if="loading">Loading...</div>
    <div v-if="error" style="color:red;">{{ error }}</div>

    <div v-if="menus.length > 0">
      <ul>
        <li v-for="menu in menus" :key="menu.id">
          <strong>{{ menu.name }}</strong>
          - Rp {{ menu.price }}
          (Stock: {{ menu.stock }})
        </li>
      </ul>
    </div>

    <div v-else-if="!loading">
      Tidak ada menu.
    </div>
  </div>
</template>