<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const menus = ref([])
const form = ref({
  id: null,
  name: '',
  price: '',
  stock: ''
})

const isEdit = ref(false)

const fetchMenus = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/menus')
    menus.value = res.data
  } catch (error) {
    console.error('Error fetching menus:', error)
    alert('Gagal memuat data menu')
  }
}

onMounted(fetchMenus)

const submitForm = async () => {
  try {
    if (isEdit.value) {
      await axios.put(
        `http://localhost:8000/api/menus/${form.value.id}`,
        form.value
      )
      alert('Menu berhasil diupdate!')
    } else {
      await axios.post('http://localhost:8000/api/menus', form.value)
      alert('Menu berhasil ditambahkan!')
    }

    resetForm()
    fetchMenus()
  } catch (error) {
    console.error('Error saving menu:', error)
    alert('Gagal menyimpan menu')
  }
}

const editMenu = (menu) => {
  form.value = { ...menu }
  isEdit.value = true
}

const deleteMenu = async (id) => {
  if (confirm('Yakin ingin menghapus menu ini?')) {
    try {
      await axios.delete(`http://localhost:8000/api/menus/${id}`)
      alert('Menu berhasil dihapus!')
      fetchMenus()
    } catch (error) {
      console.error('Error deleting menu:', error)
      alert('Gagal menghapus menu')
    }
  }
}

const resetForm = () => {
  form.value = {
    id: null,
    name: '',
    price: '',
    stock: ''
  }
  isEdit.value = false
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}
</script>

<template>
  <div class="admin-container">
    <div class="header">
      <h1>🍽️ Admin Menu</h1>
      <p>Kelola menu makanan Anda dengan mudah</p>
    </div>

    <div class="content-wrapper">
      <!-- FORM SECTION -->
      <div class="form-section">
        <div class="section-title">
          <h2>{{ isEdit ? '✏️ Edit Menu' : '➕ Tambah Menu Baru' }}</h2>
        </div>
        
        <form @submit.prevent="submitForm" class="menu-form">
          <div class="form-group">
            <label for="name">Nama Menu</label>
            <input 
              id="name"
              v-model="form.name" 
              type="text"
              placeholder="Masukkan nama menu"
              required 
            />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="price">Harga (Rp)</label>
              <input 
                id="price"
                v-model="form.price" 
                type="number" 
                placeholder="0"
                min="0"
                required 
              />
            </div>

            <div class="form-group">
              <label for="stock">Stok</label>
              <input 
                id="stock"
                v-model="form.stock" 
                type="number" 
                placeholder="0"
                min="0"
                required 
              />
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              {{ isEdit ? 'Update Menu' : 'Simpan Menu' }}
            </button>
            <button type="button" class="btn btn-secondary" @click="resetForm">
              Reset
            </button>
          </div>
        </form>
      </div>

      <!-- LIST SECTION -->
      <div class="list-section">
        <div class="section-title">
          <h2>📋 Daftar Menu</h2>
          <span class="menu-count">{{ menus.length }} menu</span>
        </div>

        <div v-if="menus.length === 0" class="empty-state">
          <p>Belum ada menu tersedia</p>
          <p class="empty-sub">Tambahkan menu baru menggunakan form di samping</p>
        </div>

        <div v-else class="table-responsive">
          <table class="menu-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Menu</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(menu, index) in menus" :key="menu.id">
                <td>{{ index + 1 }}</td>
                <td class="menu-name">{{ menu.name }}</td>
                <td class="menu-price">Rp {{ formatPrice(menu.price) }}</td>
                <td>
                  <span :class="['stock-badge', { 'stock-low': menu.stock <= 5 }]">
                    {{ menu.stock }}
                  </span>
                </td>
                <td>
                  <span :class="['status-badge', menu.stock > 0 ? 'status-available' : 'status-empty']">
                    {{ menu.stock > 0 ? 'Tersedia' : 'Habis' }}
                  </span>
                </td>
                <td class="action-buttons">
                  <button @click="editMenu(menu)" class="btn-edit">
                    Edit
                  </button>
                  <button @click="deleteMenu(menu.id)" class="btn-delete">
                    Hapus
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.admin-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 40px 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.header {
  max-width: 1400px;
  margin: 0 auto 40px;
  color: white;
}

.header h1 {
  font-size: 2.5rem;
  margin-bottom: 10px;
  font-weight: 600;
}

.header p {
  font-size: 1.1rem;
  opacity: 0.9;
}

.content-wrapper {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 380px 1fr;
  gap: 30px;
}

/* Form Section */
.form-section {
  background: white;
  border-radius: 20px;
  padding: 25px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  height: fit-content;
}

.section-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f0f0f0;
}

.section-title h2 {
  font-size: 1.3rem;
  color: #333;
  font-weight: 600;
}

.menu-count {
  background: #667eea;
  color: white;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.9rem;
}

.menu-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #555;
}

.form-group input {
  padding: 12px 15px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group input::placeholder {
  color: #999;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.form-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-top: 10px;
}

.btn {
  padding: 12px 20px;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
  background: #f0f0f0;
  color: #666;
}

.btn-secondary:hover {
  background: #e0e0e0;
}

/* List Section */
.list-section {
  background: white;
  border-radius: 20px;
  padding: 25px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.table-responsive {
  overflow-x: auto;
}

.menu-table {
  width: 100%;
  border-collapse: collapse;
}

.menu-table th {
  background: #f8f9fa;
  padding: 15px;
  text-align: left;
  font-size: 0.9rem;
  font-weight: 600;
  color: #555;
  border-bottom: 2px solid #e0e0e0;
}

.menu-table td {
  padding: 15px;
  border-bottom: 1px solid #f0f0f0;
  color: #666;
}

.menu-table tbody tr:hover {
  background: #f8f9fa;
}

.menu-name {
  font-weight: 600;
  color: #333;
}

.menu-price {
  font-weight: 600;
  color: #667eea;
}

.stock-badge {
  display: inline-block;
  padding: 5px 10px;
  border-radius: 6px;
  background: #e8f5e9;
  color: #2e7d32;
  font-weight: 600;
}

.stock-low {
  background: #fff3e0;
  color: #ed6c02;
}

.status-badge {
  display: inline-block;
  padding: 5px 10px;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
}

.status-available {
  background: #e8f5e9;
  color: #2e7d32;
}

.status-empty {
  background: #ffebee;
  color: #c62828;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-edit, .btn-delete {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-edit {
  background: #e3f2fd;
  color: #1976d2;
}

.btn-edit:hover {
  background: #bbdefb;
}

.btn-delete {
  background: #ffebee;
  color: #c62828;
}

.btn-delete:hover {
  background: #ffcdd2;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #999;
}

.empty-state p:first-child {
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 10px;
}

.empty-sub {
  font-size: 0.9rem;
  opacity: 0.8;
}

/* Responsive */
@media (max-width: 968px) {
  .content-wrapper {
    grid-template-columns: 1fr;
  }
  
  .form-section {
    order: 2;
  }
  
  .list-section {
    order: 1;
  }
}

@media (max-width: 576px) {
  .admin-container {
    padding: 20px 10px;
  }
  
  .header h1 {
    font-size: 2rem;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>