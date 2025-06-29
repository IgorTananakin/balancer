<template>
  <div class="history-page">
    <div class="header">
      <h1 class="title">История операций</h1>
      <div class="user-info">Пользователь: {{ userEmail }}</div>
    </div>

    <div class="controls">
      <div class="search">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Поиск по описанию..."
          class="search-input"
        >
        <button @click="resetSearch" class="reset-button">Сбросить</button>
      </div>
    </div>

    <div class="table-container">
      <table class="transactions-table">
        <thead>
          <tr>
            <th 
              @click="sortBy('created_at')"
              :class="{ 'active': sortField === 'created_at' }"
            >
              Дата
              <span class="sort-arrow" v-if="sortField === 'created_at'">
                {{ sortDirection === 'asc' ? '↑' : '↓' }}
              </span>
            </th>
            <th 
              @click="sortBy('type')"
              :class="{ 'active': sortField === 'type' }"
            >
              Тип
              <span class="sort-arrow" v-if="sortField === 'type'">
                {{ sortDirection === 'asc' ? '↑' : '↓' }}
              </span>
            </th>
            <th 
              @click="sortBy('amount')"
              :class="{ 'active': sortField === 'amount' }"
            >
              Сумма
              <span class="sort-arrow" v-if="sortField === 'amount'">
                {{ sortDirection === 'asc' ? '↑' : '↓' }}
              </span>
            </th>
            <th>Описание</th>
            <th>Категория</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="transaction in filteredTransactions" :key="transaction.id">
            <td>{{ formatDate(transaction.created_at) }}</td>
            <td>
              <span :class="transaction.type">{{ transaction.type === 'income' ? 'Доход' : 'Расход' }}</span>
            </td>
            <td :class="transaction.type">
              {{ transaction.type === 'income' ? '+' : '-' }}{{ transaction.amount }}
            </td>
            <td>{{ transaction.description }}</td>
            <td>{{ transaction.category }}</td>
          </tr>
          <tr v-if="filteredTransactions.length === 0">
            <td colspan="5" class="no-results">Нет операций для отображения</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="footer">
      <router-link :to="'/' + userEmail" class="back-link">
        вернуться к главной
      </router-link>
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
      error: null,
      searchQuery: '',
      sortField: 'created_at',
      sortDirection: 'desc'
    }
  },
  computed: {
    filteredTransactions() {
      let result = [...this.transactions];

      // Фильтрация по поисковому запросу
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        result = result.filter(t => 
          t.description.toLowerCase().includes(query) ||
          (t.category && t.category.toLowerCase().includes(query))
        );
      }

      // Сортировка
      result.sort((a, b) => {
        let fieldA, fieldB;

        if (this.sortField === 'created_at') {
          fieldA = new Date(a.created_at);
          fieldB = new Date(b.created_at);
        } else {
          fieldA = a[this.sortField];
          fieldB = b[this.sortField];
        }

        if (this.sortDirection === 'asc') {
          return fieldA > fieldB ? 1 : -1;
        } else {
          return fieldA < fieldB ? 1 : -1;
        }
      });

      return result;
    }
  },
  methods: {
    async fetchTransactions() {
      try {
        this.isLoading = true;
        const response = await axios.get(`/api/transactions/${this.userEmail}`);
        
        this.transactions = response.data.map(item => ({
          ...item,
          type: item.type === 'credit' ? 'income' : 'expense',
          category: this.getCategory(item.description),
          amount: parseFloat(item.amount).toFixed(2),
          created_at: item.created_at
        }));

      } catch (error) {
        console.error('Ошибка при загрузке операций:', error);
        this.error = 'Ошибка при загрузке операций';
      } finally {
        this.isLoading = false;
      }
    },
    getCategory(description) {
      if (!description) return 'Другое';
      
      //для дополнительной колонки
      const desc = description.toLowerCase();
      if (desc.includes('зарплата')) return 'Зарплата';
      if (desc.includes('пятёрочк') || desc.includes('магазин')) return 'Продукты';
      if (desc.includes('день рожд')) return 'Подарок';
      if (desc.includes('оплат') || desc.includes('заказ')) return 'Платеж';
      if (desc.includes('перевод')) return 'Перевод';
      return 'Другое';
    },
    formatDate(dateString) {
      if (!dateString) return 'Дата не указана';
      
      try {
        const [datePart, timePart] = dateString.split(' ');
        const [year, month, day] = datePart.split('-');
        const [hours, minutes, seconds] = timePart.split(':');
        
        const date = new Date(year, month - 1, day, hours, minutes, seconds);
        
        if (isNaN(date.getTime())) {
          throw new Error('Invalid date');
        }
        
        return date.toLocaleDateString('ru-RU', {
          day: 'numeric',
          month: 'long',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
        
      } catch (error) {
        console.error('Date formatting error:', error);
        return dateString;
      }
    },
    sortBy(field) {
      if (this.sortField === field) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortField = field;
        this.sortDirection = 'desc';
      }
    },
    resetSearch() {
      this.searchQuery = '';
    }
  },
  created() {
    this.fetchTransactions();
  }
}
</script>

<style scoped>
.history-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.header {
  margin-bottom: 30px;
}

.title {
  color: #2c3e50;
  margin-bottom: 5px;
}

.user-info {
  color: #7f8c8d;
  font-size: 16px;
}

.controls {
  margin-bottom: 20px;
}

.search {
  display: flex;
  align-items: center;
  gap: 10px;
}

.search-input {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  min-width: 250px;
}

.reset-button {
  padding: 8px 12px;
  background: #f5f5f5;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
}

.reset-button:hover {
  background: #e0e0e0;
}

.table-container {
  overflow-x: auto;
  margin-bottom: 30px;
}

.transactions-table {
  width: 100%;
  border-collapse: collapse;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.transactions-table th {
  background-color: #4a6bff;
  color: white;
  padding: 12px;
  text-align: left;
  cursor: pointer;
  position: relative;
}

.transactions-table th:hover {
  background-color: #3a5bef;
}

.transactions-table th.active {
  background-color: #2a4bdf;
}

.sort-arrow {
  margin-left: 5px;
}

.transactions-table td {
  padding: 12px;
  border-bottom: 1px solid #eee;
}

.transactions-table tr:hover {
  background-color: #f5f5f5;
}

.income {
  color: #27ae60;
  font-weight: 500;
}

.expense {
  color: #e74c3c;
  font-weight: 500;
}

.no-results {
  text-align: center;
  color: #95a5a6;
  font-style: italic;
  padding: 20px;
}

.footer {
  margin-top: 20px;
}

.back-link {
  color: #4a6bff;
  text-decoration: none;
  font-size: 16px;
  display: inline-flex;
  align-items: center;
}

.back-link:hover {
  text-decoration: underline;
}

@media (max-width: 768px) {
  .controls {
    flex-direction: column;
  }
  
  .search {
    width: 100%;
  }
  
  .search-input {
    flex-grow: 1;
  }
}
</style>