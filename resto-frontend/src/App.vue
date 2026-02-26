<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// State untuk Menu
const menus = ref([])
const menuForm = ref({
  id: null,
  name: '',
  price: '',
  stock: ''
})
const isEditMenu = ref(false)

// State untuk Meja
const tables = ref([])
const tableForm = ref({
  id: null,
  table_number: '',
  capacity: '',
  status: 'available'
})
const isEditTable = ref(false)

// State untuk Reservasi
const reservations = ref([])
const reservationForm = ref({
  table_id: '',
  customer_name: '',
  reservation_time: ''
})

// Fetch data
const fetchMenus = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/menus')
    menus.value = res.data
  } catch (error) {
    console.error('Error:', error)
    alert('Gagal memuat menu')
  }
}

const fetchTables = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/tables')
    tables.value = res.data
  } catch (error) {
    console.error('Error:', error)
    alert('Gagal memuat meja')
  }
}

const fetchReservations = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/reservations')
    reservations.value = res.data
  } catch (error) {
    console.error('Error:', error)
    alert('Gagal memuat reservasi')
  }
}

onMounted(() => {
  fetchMenus()
  fetchTables()
  fetchReservations()
})

// Fungsi untuk Menu
const submitMenu = async () => {
  try {
    if (isEditMenu.value) {
      await axios.put(`http://localhost:8000/api/menus/${menuForm.value.id}`, menuForm.value)
      alert('Menu berhasil diupdate!')
    } else {
      await axios.post('http://localhost:8000/api/menus', menuForm.value)
      alert('Menu berhasil ditambahkan!')
    }
    resetMenuForm()
    fetchMenus()
  } catch (error) {
    console.error('Error:', error)
    alert('Gagal menyimpan menu')
  }
}

const editMenu = (menu) => {
  menuForm.value = { ...menu }
  isEditMenu.value = true
}

const deleteMenu = async (id) => {
  if (confirm('Yakin ingin menghapus menu ini?')) {
    try {
      await axios.delete(`http://localhost:8000/api/menus/${id}`)
      alert('Menu berhasil dihapus!')
      fetchMenus()
    } catch (error) {
      console.error('Error:', error)
      alert('Gagal menghapus menu')
    }
  }
}

const resetMenuForm = () => {
  menuForm.value = { id: null, name: '', price: '', stock: '' }
  isEditMenu.value = false
}

// Fungsi untuk Meja
const submitTable = async () => {
  try {
    if (isEditTable.value) {
      await axios.put(`http://localhost:8000/api/tables/${tableForm.value.id}`, tableForm.value)
      alert('Meja berhasil diupdate!')
    } else {
      await axios.post('http://localhost:8000/api/tables', tableForm.value)
      alert('Meja berhasil ditambahkan!')
    }
    resetTableForm()
    fetchTables()
  } catch (error) {
    console.error('Error:', error)
    alert('Gagal menyimpan meja')
  }
}

const editTable = (table) => {
  tableForm.value = { ...table }
  isEditTable.value = true
}

const deleteTable = async (id) => {
  if (confirm('Yakin ingin menghapus meja ini?')) {
    try {
      await axios.delete(`http://localhost:8000/api/tables/${id}`)
      alert('Meja berhasil dihapus!')
      fetchTables()
    } catch (error) {
      console.error('Error:', error)
      alert('Gagal menghapus meja')
    }
  }
}

const resetTableForm = () => {
  tableForm.value = { id: null, table_number: '', capacity: '', status: 'available' }
  isEditTable.value = false
}

// Fungsi untuk Reservasi
const submitReservation = async () => {
  try {
    await axios.post('http://localhost:8000/api/reservations', reservationForm.value)
    alert('Reservasi berhasil!')
    fetchReservations()
    fetchTables() // Refresh status meja
    reservationForm.value = {
      table_id: '',
      customer_name: '',
      reservation_time: ''
    }
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal membuat reservasi')
  }
}

const deleteReservation = async (id) => {
  if (confirm('Yakin ingin membatalkan reservasi ini?')) {
    try {
      await axios.delete(`http://localhost:8000/api/reservations/${id}`)
      alert('Reservasi dibatalkan')
      fetchReservations()
      fetchTables() // Refresh status meja
    } catch (error) {
      console.error('Error:', error)
      alert('Gagal membatalkan reservasi')
    }
  }
}

// Helper
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const formatDateTime = (datetime) => {
  return new Date(datetime).toLocaleString('id-ID')
}
</script>

<template>
  <div class="container">
    <h1>🍽️ Admin Panel - Restaurant Management</h1>
    
    <!-- Section Menu -->
    <div class="section">
      <h2>📋 Manajemen Menu</h2>
      
      <form @submit.prevent="submitMenu" class="form">
        <input v-model="menuForm.name" placeholder="Nama Menu" required />
        <input v-model="menuForm.price" type="number" placeholder="Harga" required />
        <input v-model="menuForm.stock" type="number" placeholder="Stok" required />
        <div class="actions">
          <button type="submit" class="btn-primary">{{ isEditMenu ? 'Update' : 'Simpan' }} Menu</button>
          <button type="button" @click="resetMenuForm" class="btn-secondary">Reset</button>
        </div>
      </form>

      <table v-if="menus.length > 0">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="menu in menus" :key="menu.id">
            <td>{{ menu.name }}</td>
            <td>Rp {{ formatPrice(menu.price) }}</td>
            <td>
              <span :class="['badge', menu.stock <= 5 ? 'badge-warning' : 'badge-success']">
                {{ menu.stock }}
              </span>
            </td>
            <td>
              <button @click="editMenu(menu)" class="btn-edit">Edit</button>
              <button @click="deleteMenu(menu.id)" class="btn-delete">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else class="empty">Belum ada menu</p>
    </div>

    <!-- Section Meja -->
    <div class="section">
      <h2>🪑 Manajemen Meja</h2>
      
      <form @submit.prevent="submitTable" class="form">
        <input v-model="tableForm.table_number" placeholder="Nomor Meja" required />
        <input v-model="tableForm.capacity" type="number" placeholder="Kapasitas" required />
        <select v-model="tableForm.status">
          <option value="available">Tersedia</option>
          <option value="reserved">Dipesan</option>
        </select>
        <div class="actions">
          <button type="submit" class="btn-primary">{{ isEditTable ? 'Update' : 'Simpan' }} Meja</button>
          <button type="button" @click="resetTableForm" class="btn-secondary">Reset</button>
        </div>
      </form>

      <table v-if="tables.length > 0">
        <thead>
          <tr>
            <th>No Meja</th>
            <th>Kapasitas</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="table in tables" :key="table.id">
            <td>Meja {{ table.table_number }}</td>
            <td>{{ table.capacity }} orang</td>
            <td>
              <span :class="['badge', table.status === 'available' ? 'badge-success' : 'badge-danger']">
                {{ table.status === 'available' ? 'Tersedia' : 'Dipesan' }}
              </span>
            </td>
            <td>
              <button @click="editTable(table)" class="btn-edit">Edit</button>
              <button @click="deleteTable(table.id)" class="btn-delete">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else class="empty">Belum ada meja</p>
    </div>

    <!-- Section Reservasi -->
    <div class="section">
      <h2>📅 Manajemen Reservasi</h2>
      
      <form @submit.prevent="submitReservation" class="form">
        <input 
          v-model="reservationForm.customer_name" 
          placeholder="Nama Customer" 
          required 
        />
        
        <select v-model="reservationForm.table_id" required>
          <option value="">Pilih Meja</option>
          <option 
            v-for="table in tables" 
            :key="table.id" 
            :value="table.id"
            :disabled="table.status === 'reserved'"
          >
            Meja {{ table.table_number }} ({{ table.capacity }} org) - 
            {{ table.status === 'available' ? 'Tersedia' : 'Dipesan' }}
          </option>
        </select>

        <input 
          type="datetime-local" 
          v-model="reservationForm.reservation_time" 
          required 
        />

        <div class="actions">
          <button type="submit" class="btn-primary">Buat Reservasi</button>
          <button type="button" @click="reservationForm = { table_id: '', customer_name: '', reservation_time: '' }" class="btn-secondary">
            Reset
          </button>
        </div>
      </form>

      <table v-if="reservations.length > 0">
        <thead>
          <tr>
            <th>Customer</th>
            <th>Meja</th>
            <th>Waktu Reservasi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="res in reservations" :key="res.id">
            <td>{{ res.customer_name }}</td>
            <td>Meja {{ res.table.table_number }} ({{ res.table.capacity }} org)</td>
            <td>{{ formatDateTime(res.reservation_time) }}</td>
            <td>
              <button @click="deleteReservation(res.id)" class="btn-delete">Batalkan</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else class="empty">Belum ada reservasi</p>
    </div>
  </div>
</template>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  font-family: Arial, sans-serif;
}

h1 {
  color: #333;
  margin-bottom: 30px;
  text-align: center;
}

.section {
  background: #f9f9f9;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 30px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section h2 {
  color: #555;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #ddd;
}

.form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 10px;
  margin-bottom: 20px;
  background: white;
  padding: 15px;
  border-radius: 6px;
}

.form input, .form select {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form input:focus, .form select:focus {
  outline: none;
  border-color: #4CAF50;
}

.actions {
  display: flex;
  gap: 10px;
  grid-column: 1 / -1;
}

button {
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  font-weight: bold;
  transition: all 0.3s;
}

.btn-primary {
  background: #4CAF50;
  color: white;
}

.btn-primary:hover {
  background: #45a049;
}

.btn-secondary {
  background: #f0f0f0;
  color: #333;
}

.btn-secondary:hover {
  background: #e0e0e0;
}

.btn-edit {
  background: #2196F3;
  color: white;
  margin-right: 5px;
  padding: 5px 10px;
}

.btn-edit:hover {
  background: #1976D2;
}

.btn-delete {
  background: #f44336;
  color: white;
  padding: 5px 10px;
}

.btn-delete:hover {
  background: #d32f2f;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 6px;
  overflow: hidden;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background: #f2f2f2;
  font-weight: bold;
  color: #333;
}

tr:hover {
  background: #f5f5f5;
}

.badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
  display: inline-block;
}

.badge-success {
  background: #e8f5e9;
  color: #2e7d32;
}

.badge-warning {
  background: #fff3e0;
  color: #ed6c02;
}

.badge-danger {
  background: #ffebee;
  color: #c62828;
}

.empty {
  text-align: center;
  color: #999;
  padding: 40px;
  background: white;
  border-radius: 4px;
}

@media (max-width: 768px) {
  .form {
    grid-template-columns: 1fr;
  }
  
  table {
    font-size: 14px;
  }
  
  th, td {
    padding: 8px;
  }
}
</style>
