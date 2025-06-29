<template>
  <div class="dashboard">
    <h1 class="dashboard__title">Главная страница</h1>
    
    <div class="dashboard-controls">
      <div class="refresh-controls">
        
        <span class="last-update">Интервал обновления 3 секунды</span>
      </div>
    </div>
    
    <div class="balance-card">
      <div class="balance-card__header">
        <h2 class="balance-card__title">Текущий баланс</h2>
        <span class="balance-card__date">Сегодня, {{ currentDate }}</span>
      </div>
      <div class="balance-card__amount">{{ formattedBalance }} ₽</div>
    </div>
    
    <div class="transactions">
      <div class="transactions__header">
        <h2 class="transactions__title">Последние операции</h2>
        <div class="transactions__controls">
          <select v-model="transactionLimit" class="transactions__limit-select">
            <option value="5">5 операций</option>
            <option value="10">10 операций</option>
            <option value="20">20 операций</option>
            <option value="0">Все операции</option>
          </select>
          <router-link 
            :to="'/history/' + userEmail" 
            class="transactions__link"
            >
            Вся история операций
        </router-link>
        </div>
      </div>
      
      <div v-if="isLoading" class="loading-indicator">
        <div class="loader"></div>
        <span>Обновление данных...</span>
      </div>
      
      
      
      <div class="transactions__list">
        <div v-for="(transaction, index) in lastTransactions" :key="index" class="transaction-item">
          <div class="transaction-item__info">
            <div class="transaction-item__category">{{ transaction.category }}</div>
            <div class="transaction-item__description">{{ transaction.description }}</div>
            <div class="transaction-item__date">{{ formatTransactionDate(transaction.date) }}</div>
          </div>
          <div 
            class="transaction-item__amount" 
            :class="{ 'transaction-item__amount--income': transaction.type === 'income', 'transaction-item__amount--expense': transaction.type === 'expense' }"
          >
            {{ transaction.type === 'income' ? '+' : '-' }}{{ transaction.amount }} ₽
          </div>
        </div>
        
        <div v-if="lastTransactions.length === 0 && !isLoading" class="transactions__empty">
          Нет операций для отображения
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Dashboard',
  data() {
    return {
      balance: 0,
      lastTransactions: [],
      isLoading: false,
      error: null,
      transactionLimit: 5,
      userEmail: 'ivan@example.com',
      refreshInterval: null,
      userEmail: window.userEmail || 'ivan@example.com',
      refreshTime: 3000 
    }
  },
  methods: {
    getCategoryByDescription(description) {
      const descriptionLower = description.toLowerCase();
      
      if (descriptionLower.includes('зарплата')) return 'Зарплата';
      if (descriptionLower.includes('супермаркет') || 
          descriptionLower.includes('пятёрочк') || 
          descriptionLower.includes('магазин')) return 'Продукты';
      if (descriptionLower.includes('ресторан') || 
          descriptionLower.includes('кафе')) return 'Кафе';
      if (descriptionLower.includes('такси') || 
          descriptionLower.includes('транспорт')) return 'Транспорт';
      if (descriptionLower.includes('день рожд') || 
          descriptionLower.includes('подарок')) return 'Подарок';
      if (descriptionLower.includes('перевод')) return 'Перевод';
      if (descriptionLower.includes('оплат')) return 'Платеж';
      
      return 'Другое';
    },
    formatTransactionDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    async fetchData() {
      await Promise.all([this.fetchBalance(), this.fetchTransactions()]);
    },
    async fetchTransactions() {
      try {
        this.isLoading = true;
        this.error = null;
        
        const params = {};
        if (this.transactionLimit > 0) {
          params.limit = this.transactionLimit;
        }
        
        const transactionsResponse = await axios.get(`/api/transactions/${this.userEmail}`, { params });
        
        this.lastTransactions = transactionsResponse.data.map(transaction => ({
          id: transaction.id,
          amount: parseFloat(transaction.amount),
          type: transaction.type === 'credit' ? 'income' : 'expense',
          description: transaction.description,
          date: transaction.created_at,
          category: this.getCategoryByDescription(transaction.description)
        }));

      } catch (error) {
        console.error('Ошибка при загрузке транзакций:', error);
        this.error = 'Не удалось загрузить транзакции';
      } finally {
        this.isLoading = false;
      }
    },
    async fetchBalance() {
      try {
        const balanceResponse = await axios.get(`/api/balance/${this.userEmail}`);
        this.balance = parseFloat(balanceResponse.data.balance);
      } catch (error) {
        console.error('Ошибка при загрузке баланса:', error);
        this.error = 'Не удалось загрузить баланс';
      }
    },
    startAutoRefresh() {
      // Очищаем предыдущий интервал, если он был
      if (this.refreshInterval) {
        clearInterval(this.refreshInterval);
      }
      
      // Устанавливаем новый интервал
      this.refreshInterval = setInterval(() => {
        this.fetchData();
      }, this.refreshTime);
    },
    stopAutoRefresh() {
      if (this.refreshInterval) {
        clearInterval(this.refreshInterval);
        this.refreshInterval = null;
      }
    }
  },
  async created() {
    await this.fetchData();
    this.startAutoRefresh();
  },
  beforeDestroy() {
    this.stopAutoRefresh();
  },
  watch: {
    transactionLimit() {
      this.fetchTransactions();
    },
    refreshTime() {
      this.startAutoRefresh();
    }
  },
  computed: {
    formattedBalance() {
      return this.balance.toLocaleString('ru-RU', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    },
    currentDate() {
      return new Date().toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    },
    lastUpdateTime() {
      return new Date().toLocaleTimeString('ru-RU', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
    }
  }
}
</script>

<style scoped>
/* Добавляем новые стили для элементов управления */
.transactions__controls {
  display: flex;
  align-items: center;
  gap: 15px;
}

.transactions__limit-select {
  padding: 5px 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
  font-size: 14px;
}

/* Остальные стили остаются без изменений */
.dashboard {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dashboard__title {
  color: #2c3e50;
  margin-bottom: 30px;
  text-align: center;
}

.balance-card {
  background: linear-gradient(135deg, #4a6bff, #6a11cb);
  color: white;
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 30px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.balance-card__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.balance-card__title {
  font-size: 18px;
  font-weight: 500;
  margin: 0;
}

.balance-card__date {
  font-size: 14px;
  opacity: 0.9;
}

.balance-card__amount {
  font-size: 36px;
  font-weight: 700;
}

.transactions__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.transactions__title {
  font-size: 20px;
  color: #2c3e50;
  margin: 0;
}

.transactions__link {
  color: #4a6bff;
  text-decoration: none;
  font-size: 14px;
}

.transactions__link:hover {
  text-decoration: underline;
}

.transactions__list {
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.transaction-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #f0f0f0;
}

.transaction-item:last-child {
  border-bottom: none;
}

.transaction-item__info {
  flex: 1;
}

.transaction-item__category {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 5px;
}

.transaction-item__description {
  font-size: 14px;
  color: #7f8c8d;
  margin-bottom: 5px;
}

.transaction-item__date {
  font-size: 12px;
  color: #95a5a6;
}

.transaction-item__amount {
  font-weight: 600;
  font-size: 16px;
}

.transaction-item__amount--income {
  color: #27ae60;
}

.transaction-item__amount--expense {
  color: #e74c3c;
}

.transactions__empty {
  padding: 20px;
  text-align: center;
  color: #95a5a6;
  font-style: italic;
}
</style>