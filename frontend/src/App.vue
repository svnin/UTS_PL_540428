<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

const email = ref('test@example.com')
const password = ref('secret123')
const token = ref(localStorage.getItem('token') || '')

const products = ref([])
const name = ref('')
const price = ref('')
const stock = ref('')

const login = async () => {
  try {
    const res = await axios.post(`${API_URL}/login`, {
      email: email.value,
      password: password.value
    })
    token.value = res.data.access_token
    localStorage.setItem('token', token.value)
    fetchProducts()
  } catch (err) {
    alert('Login Gagal')
  }
}

const logout = async () => {
  token.value = ''
  localStorage.removeItem('token')
  products.value = []
}

const fetchProducts = async () => {
  if (!token.value) return
  try {
    const res = await axios.get(`${API_URL}/products`, {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    products.value = res.data
  } catch (err) {
    console.error(err)
  }
}

const addProduct = async () => {
  try {
    await axios.post(`${API_URL}/products`, {
      name: name.value,
      price: Number(price.value),
      stock: Number(stock.value)
    }, {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    name.value = ''
    price.value = ''
    stock.value = ''
    fetchProducts()
  } catch (err) {
    alert('Gagal menambah produk')
  }
}

const deleteProduct = async (id) => {
  try {
    await axios.delete(`${API_URL}/products/${id}`, {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    fetchProducts()
  } catch (err) {
    alert('Gagal menghapus produk')
  }
}

onMounted(() => {
  if (token.value) fetchProducts()
})
</script>

<template>
  <div style="padding: 20px; font-family: sans-serif;">
    <h2>UTS PL - Aplikasi Product</h2>
    
    <div v-if="!token">
      <h3>Login</h3>
      <input v-model="email" placeholder="Email" /> <br/><br/>
      <input v-model="password" type="password" placeholder="Password" /> <br/><br/>
      <button @click="login">Login</button>
    </div>

    <div v-else>
      <button @click="logout">Logout</button>
      <hr />

      <h3>Tambah Produk</h3>
      <form @submit.prevent="addProduct">
        <input v-model="name" placeholder="Nama Produk" required />
        <input v-model="price" type="number" placeholder="Harga" required />
        <input v-model="stock" type="number" placeholder="Stok" required />
        <button type="submit">Tambah</button>
      </form>

      <h3>Daftar Produk</h3>
      <ul>
        <li v-for="p in products" :key="p.id">
          {{ p.name }} - Rp{{ p.price }} (Stok: {{ p.stock }})
          <button @click="deleteProduct(p.id)">Hapus</button>
        </li>
      </ul>
    </div>
  </div>
</template>