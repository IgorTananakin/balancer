<template>
  <div class="login-page">
    <div class="login-container">
      <h1 class="title">Вход в систему</h1>
      
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="email">Email:</label>
          <input
            id="email"
            v-model="email"
            type="email"
            placeholder="Введите ваш email"
            required
            class="input-field"
          >
        </div>
        
        <button type="submit" class="login-button" :disabled="isLoading">
          <span v-if="!isLoading">Войти</span>
          <span v-else>Проверка...</span>
        </button>
        
        <div v-if="error" class="error-message">{{ error }}</div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LoginPage',
  data() {
    return {
      email: '',
      isLoading: false,
      error: null
    }
  },
  methods: {
    async handleLogin() {
      try {
        this.isLoading = true;
        this.error = null;
        
        // Проверяем существование пользователя
        const response = await axios.get(`/api/users/check?email=${encodeURIComponent(this.email)}`);
        
        if (response.data.exists) {
          // Перенаправляем на страницу пользователя
          this.$router.push(`/${this.email}`);
        } else {
          this.error = 'Пользователь с таким email не найден';
        }
      } catch (error) {
        console.error('Login error:', error);
        this.error = 'Ошибка при проверке пользователя';
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>

<style scoped>
.login-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.login-container {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
}

.title {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 1.5rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.input-field {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.login-button {
  padding: 0.75rem;
  background-color: #4a6bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.login-button:hover:not(:disabled) {
  background-color: #3a5bef;
}

.login-button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.error-message {
  color: #e74c3c;
  text-align: center;
  margin-top: 1rem;
}
</style>