<template>
  <div class="history-page">
    <h1 class="history-title">История операций</h1>
    <div class="user-email">Пользователь: {{ userEmail }}</div>
    
    <div class="controls">
      <router-link 
        :to="'/' + userEmail" 
        class="back-link"
      >
         к главной
      </router-link>
    </div>
    
    <div v-if="isLoading" class="loading">Загрузка...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else class="transactions-list">
      <div v-for="transaction in transactions" :key="transaction.id" class="transaction">
        <div class="transaction-info">
          <div class="transaction-date">{{ formatDate(transaction.created_at) }}</div>
          <div class="transaction-description">{{ transaction.description }}</div>
          <div class="transaction-category">{{ getCategory(transaction.description) }}</div>
        </div>
        <div 
          class="transaction-amount"
          :class="{
            'income': transaction.type === 'income',
            'expense': transaction.type === 'expense'
          }"
        >
          {{ transaction.type === 'income' ? '+' : '-' }}{{ transaction.amount }} ₽
        </div>
      </div>
      
      <div v-if="transactions.length === 0" class="empty">
        Нет операций для отображения
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'HistoryPage',
  props: {
    userEmail: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      transactions: [],
      isLoading: true,
      error: null
    }
  },
  async created() {
    await this.fetchTransactions();
  },
  methods: {
    async fetchTransactions() {
      try {
        this.isLoading = true;
        const response = await axios.get(`/api/transactions/${this.userEmail}`);
        this.transactions = response.data.map(t => ({
          ...t,
          type: t.type === 'credit' ? 'income' : 'expense',
          amount: parseFloat(t.amount)
        }));
      } catch (error) {
        this.error = 'Ошибка при загрузке операций';
        console.error(error);
      } finally {
        this.isLoading = false;
      }
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    getCategory(description) {
      const desc = description.toLowerCase();
      if (desc.includes('зарплата')) return 'Зарплата';
      if (desc.includes('магазин') || desc.includes('продукты')) return 'Продукты';
      if (desc.includes('кафе') || desc.includes('ресторан')) return 'Кафе';
      if (desc.includes('транспорт') || desc.includes('такси')) return 'Транспорт';
      if (desc.includes('подарок')) return 'Подарок';
      return 'Другое';
    }
  }
}
</script>

<style scoped>
.history-page {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.history-title {
  color: #2c3e50;
  margin-bottom: 10px;
}

.user-email {
  color: #7f8c8d;
  margin-bottom: 20px;
}

.controls {
  margin-bottom: 20px;
}

.back-link {
  color: #4a6bff;
  text-decoration: none;
}

.back-link:hover {
  text-decoration: underline;
}

.transactions-list {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.transaction {
  display: flex;
  justify-content: space-between;
  padding: 15px;
  border-bottom: 1px solid #eee;
}

.transaction:last-child {
  border-bottom: none;
}

.transaction-info {
  flex: 1;
}

.transaction-date {
  font-size: 14px;
  color: #95a5a6;
  margin-bottom: 5px;
}

.transaction-description {
  font-weight: 500;
  margin-bottom: 5px;
}

.transaction-category {
  font-size: 14px;
  color: #7f8c8d;
}

.transaction-amount {
  font-weight: 600;
}

.transaction-amount.income {
  color: #27ae60;
}

.transaction-amount.expense {
  color: #e74c3c;
}

.loading, .error, .empty {
  padding: 20px;
  text-align: center;
}

.error {
  color: #e74c3c;
}

.empty {
  color: #95a5a6;
  font-style: italic;
}
</style>