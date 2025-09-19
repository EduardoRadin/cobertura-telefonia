<template>
  <div id="app">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-center mb-8 text-blue-600">
        Sistema de Cobertura Telefônica
      </h1>
      
      <!-- Controles de Importação -->
      <div class="bg-gray-100 p-6 rounded-lg mb-8">
        <h2 class="text-xl font-semibold mb-4">Importar Dados</h2>
        <div class="flex gap-4">
          <button 
            @click="importLigueAi" 
            :disabled="loading.importLigueAi"
            class="bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white px-4 py-2 rounded"
          >
            {{ loading.importLigueAi ? 'Importando...' : 'Importar Ligue Aí' }}
          </button>
          <button 
            @click="importTipBrasil" 
            :disabled="loading.importTipBrasil"
            class="bg-green-500 hover:bg-green-600 disabled:bg-gray-400 text-white px-4 py-2 rounded"
          >
            {{ loading.importTipBrasil ? 'Importando...' : 'Importar TIP Brasil' }}
          </button>
        </div>
      </div>

      <!-- Barra de Pesquisa -->
      <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold mb-4">Pesquisar Cobertura</h2>
        <div class="flex gap-4 mb-4">
          <input
            v-model="searchQuery"
            @input="search"
            type="text"
            placeholder="Digite cidade, estado ou CEP..."
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <select v-model="searchType" @change="search" class="px-4 py-2 border border-gray-300 rounded-lg">
            <option value="all">Todas as fontes</option>
            <option value="ligue_ai">Apenas Ligue Aí</option>
            <option value="tip_brasil">Apenas TIP Brasil</option>
          </select>
        </div>
        
        <!-- Informações do CEP -->
        <div v-if="searchResults.via_cep" class="bg-blue-50 p-4 rounded-lg mb-4">
          <h3 class="font-semibold text-blue-800">Informações do CEP</h3>
          <p><strong>CEP:</strong> {{ searchResults.via_cep.cep }}</p>
          <p><strong>Logradouro:</strong> {{ searchResults.via_cep.logradouro }}</p>
          <p><strong>Bairro:</strong> {{ searchResults.via_cep.bairro }}</p>
          <p><strong>Cidade:</strong> {{ searchResults.via_cep.localidade }}</p>
          <p><strong>Estado:</strong> {{ searchResults.via_cep.uf }}</p>
        </div>
      </div>

      <!-- Resultados da Pesquisa -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Ligue Aí -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold mb-4 text-blue-600">
            Ligue Aí ({{ searchResults.ligue_ai.length }} resultados)
          </h3>
          <div v-if="loading.search" class="text-center py-4">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
          </div>
          <div v-else-if="searchResults.ligue_ai.length === 0" class="text-gray-500 text-center py-4">
            Nenhum resultado encontrado
          </div>
          <div v-else class="space-y-2 max-h-96 overflow-y-auto">
            <div 
              v-for="item in searchResults.ligue_ai" 
              :key="item.id"
              class="p-3 bg-gray-50 rounded border"
            >
              <p><strong>Município:</strong> {{ item.municipio }}</p>
              <p><strong>UF:</strong> {{ item.uf }}</p>
              <p><strong>CN:</strong> {{ item.cn }}</p>
            </div>
          </div>
        </div>

        <!-- TIP Brasil -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold mb-4 text-green-600">
            TIP Brasil ({{ searchResults.tip_brasil.length }} resultados)
          </h3>
          <div v-if="loading.search" class="text-center py-4">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
          </div>
          <div v-else-if="searchResults.tip_brasil.length === 0" class="text-gray-500 text-center py-4">
            Nenhum resultado encontrado
          </div>
          <div v-else class="space-y-2 max-h-96 overflow-y-auto">
            <div 
              v-for="item in searchResults.tip_brasil" 
              :key="item.id"
              class="p-3 bg-gray-50 rounded border"
            >
              <p><strong>Município:</strong> {{ item.municipio }}</p>
              <p><strong>Estado:</strong> {{ item.sigla_estado }}</p>
              <p><strong>CN:</strong> {{ item.cn }}</p>
              <p><strong>CNL:</strong> {{ item.cnl }}</p>
              <p><strong>Área Local:</strong> {{ item.area_local }}</p>
              <p><strong>Maestro:</strong> {{ item.maestro }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'App',
  data() {
    return {
      searchQuery: '',
      searchType: 'all',
      searchResults: {
        ligue_ai: [],
        tip_brasil: [],
        via_cep: null
      },
      loading: {
        search: false,
        importLigueAi: false,
        importTipBrasil: false
      }
    }
  },
  methods: {
    async search() {
      if (!this.searchQuery.trim()) {
        this.searchResults = {
          ligue_ai: [],
          tip_brasil: [],
          via_cep: null
        }
        return
      }

      this.loading.search = true
      
      try {
        const response = await axios.get('/search', {
          params: {
            query: this.searchQuery,
            type: this.searchType
          }
        })
        
        this.searchResults = response.data
      } catch (error) {
        console.error('Erro na pesquisa:', error)
        alert('Erro ao realizar a pesquisa')
      } finally {
        this.loading.search = false
      }
    },
    
    async importLigueAi() {
      this.loading.importLigueAi = true
      
      try {
        const response = await axios.post('/scrape/ligue-ai')
        alert(`Importação concluída! ${response.data.count} registros importados.`)
      } catch (error) {
        console.error('Erro na importação:', error)
        alert('Erro ao importar dados do Ligue Aí')
      } finally {
        this.loading.importLigueAi = false
      }
    },
    
    async importTipBrasil() {
      this.loading.importTipBrasil = true
      
      try {
        const response = await axios.post('/scrape/tip-brasil')
        alert(`Importação concluída! ${response.data.count} registros importados.`)
      } catch (error) {
        console.error('Erro na importação:', error)
        alert('Erro ao importar dados da TIP Brasil')
      } finally {
        this.loading.importTipBrasil = false
      }
    }
  }
}
</script>

<style>
/* Estilos básicos */
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f8fafc;
}

.container {
  max-width: 1200px;
}

/* Animações */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
