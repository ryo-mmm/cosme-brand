<template>
  <div style="max-width:680px; margin:0 auto; padding:2rem 1.5rem;">

    <!-- Loading -->
    <div v-if="store.loading && !store.result" style="text-align:center; padding:4rem 0;">
      <div class="spinner"></div>
      <p style="margin-top:1.5rem; font-size:0.85rem; color:#8A9899; letter-spacing:0.1em;">
        {{ store.result ? '結果を処理中...' : '質問を読み込んでいます...' }}
      </p>
    </div>

    <!-- Error -->
    <div v-else-if="store.error" style="text-align:center; padding:4rem 0; color:#c0392b;">
      <p>{{ store.error }}</p>
      <button @click="store.fetchQuestions()" class="btn-secondary" style="margin-top:1.5rem;">再試行</button>
    </div>

    <!-- Result Screen -->
    <div v-else-if="store.result" class="result-screen">
      <div style="text-align:center; margin-bottom:3rem;">
        <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:1rem; text-transform:uppercase;">Diagnosis Result</p>
        <h2 style="font-family:'Noto Serif JP', serif; font-size:1.8rem; font-weight:300; color:#2E3A3B; margin-bottom:0.5rem;">
          あなたの肌タイプは
        </h2>
        <div class="skin-type-badge">
          {{ store.result.skin_type_label }}
        </div>
        <p style="font-size:0.85rem; line-height:1.9; color:#5A6B6C; margin-top:1.5rem; max-width:500px; margin-left:auto; margin-right:auto;">
          {{ skinTypeDescription[store.result.skin_type] }}
        </p>
      </div>

      <!-- Recommended Products -->
      <div v-if="store.result.products && store.result.products.length">
        <h3 style="font-family:'Noto Serif JP', serif; font-size:1.1rem; font-weight:400; color:#2E3A3B; text-align:center; margin-bottom:2rem;">
          あなたにおすすめのアイテム
        </h3>
        <div class="products-grid">
          <a
            v-for="product in store.result.products"
            :key="product.id"
            :href="`/products/${product.slug}`"
            class="product-card"
          >
            <div class="product-img-wrap">
              <svg width="36" height="36" fill="none" stroke="#C4A882" stroke-width="0.8" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
            <div style="padding:1.25rem;">
              <p style="font-size:0.65rem; letter-spacing:0.15em; color:#8A9899; margin-bottom:0.4rem; text-transform:uppercase;">{{ product.category }}</p>
              <h4 style="font-family:'Noto Serif JP', serif; font-size:0.95rem; color:#2E3A3B; margin-bottom:0.75rem;">{{ product.name }}</h4>
              <p style="font-size:0.95rem; color:#4A5859; font-weight:500;">¥{{ formatPrice(product.subscription_price) }}<span style="font-size:0.7rem; font-weight:400;">/月</span></p>
            </div>
          </a>
        </div>
        <div style="text-align:center; margin-top:2rem; display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
          <a
            :href="`/products?skin_type=${store.result.skin_type}`"
            class="btn-primary"
          >このセットで定期便を始める</a>
          <button @click="store.reset(); store.fetchQuestions()" class="btn-secondary">もう一度診断する</button>
        </div>
      </div>
      <div v-else style="text-align:center; margin-top:2rem;">
        <p style="font-size:0.85rem; color:#5A6B6C; margin-bottom:1.5rem;">現在、おすすめ商品を準備中です。</p>
        <a href="/products" class="btn-primary">すべての商品を見る</a>
      </div>
    </div>

    <!-- Diagnosis Quiz -->
    <div v-else-if="store.questions.length > 0">
      <!-- Progress Bar -->
      <div style="margin-bottom:3rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
          <span style="font-size:0.7rem; letter-spacing:0.1em; color:#8A9899;">
            {{ store.currentStep + 1 }} / {{ store.totalSteps }}
          </span>
          <span style="font-size:0.7rem; color:#8A9899;">{{ store.progressPercent }}%</span>
        </div>
        <div style="height:2px; background:#E0E0D8; border-radius:1px; overflow:hidden;">
          <div
            :style="{ width: store.progressPercent + '%' }"
            style="height:100%; background:#4A5859; transition:width 0.4s ease;"
          ></div>
        </div>
      </div>

      <!-- Question -->
      <transition name="slide-fade" mode="out-in">
        <div :key="store.currentStep" v-if="store.currentQuestion">
          <h2 style="font-family:'Noto Serif JP', serif; font-size:1.3rem; font-weight:300; color:#2E3A3B; line-height:1.8; margin-bottom:2.5rem; text-align:center;">
            Q{{ store.currentStep + 1 }}. {{ store.currentQuestion.text }}
          </h2>
          <div class="options-grid">
            <button
              v-for="option in store.currentQuestion.options"
              :key="option.label"
              @click="selectAndAdvance(store.currentQuestion.id, option.score, option.label)"
              :class="['option-btn', store.answers[store.currentQuestion.id]?.label === option.label ? 'selected' : '']"
            >
              {{ option.label }}
            </button>
          </div>
        </div>
      </transition>

      <!-- Navigation -->
      <div style="display:flex; justify-content:space-between; align-items:center; margin-top:3rem;">
        <button
          @click="store.prevStep()"
          v-if="store.currentStep > 0"
          class="btn-nav"
        >← 前の質問</button>
        <span v-else></span>
        <button
          @click="handleNext()"
          v-if="store.answers[store.currentQuestion?.id] !== undefined"
          :disabled="store.loading"
          class="btn-primary"
        >
          {{ store.currentStep < store.totalSteps - 1 ? '次の質問 →' : '診断結果を見る' }}
        </button>
      </div>
    </div>

    <!-- Initial loading placeholder -->
    <div v-else style="text-align:center; padding:4rem 0;">
      <div class="spinner"></div>
    </div>

  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useDiagnosisStore } from '../stores/diagnosis';

const store = useDiagnosisStore();

const skinTypeDescription = {
  dry: '水分が不足しがちな乾燥肌タイプです。保湿力の高い成分を中心に、肌のバリア機能をしっかりサポートするケアが大切です。',
  oily: '皮脂の分泌が活発なオイリー肌タイプです。毛穴の詰まりを防ぎながら、水分と油分のバランスを整えるケアが重要です。',
  combination: 'Tゾーンは皮脂が多く、頬などは乾燥しやすい混合肌タイプです。部位に応じたバランスの良いケアが必要です。',
  sensitive: '外部刺激に反応しやすい敏感肌タイプです。低刺激・無添加成分を使用し、肌への負担を最小限にしたケアが求められます。',
};

onMounted(async () => {
  await store.fetchQuestions();
});

function selectAndAdvance(questionId, score, label) {
  store.selectAnswer(questionId, score, label);
}

async function handleNext() {
  if (store.currentStep < store.totalSteps - 1) {
    store.nextStep();
  } else {
    await store.submitDiagnosis();
  }
}

function formatPrice(price) {
  return Number(price).toLocaleString('ja-JP');
}
</script>

<style scoped>
.spinner {
  width: 36px;
  height: 36px;
  border: 2px solid #E0E0D8;
  border-top-color: #4A5859;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto;
}
@keyframes spin { to { transform: rotate(360deg); } }

.options-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
@media (max-width: 480px) {
  .options-grid { grid-template-columns: 1fr; }
}

.option-btn {
  padding: 1.1rem 1.5rem;
  border: 1px solid #D8D4CC;
  background: #fff;
  color: #2E3A3B;
  font-size: 0.85rem;
  line-height: 1.5;
  cursor: pointer;
  border-radius: 4px;
  transition: all 0.2s;
  text-align: left;
  font-family: 'Noto Sans JP', sans-serif;
}
.option-btn:hover {
  border-color: #4A5859;
  background: #F5F5F0;
}
.option-btn.selected {
  border-color: #4A5859;
  background: #4A5859;
  color: #fff;
}

.btn-primary {
  display: inline-block;
  background: #4A5859;
  color: #fff;
  padding: 0.875rem 2rem;
  font-size: 0.8rem;
  letter-spacing: 0.1em;
  border: none;
  border-radius: 2px;
  cursor: pointer;
  text-decoration: none;
  transition: opacity 0.2s;
  font-family: 'Noto Sans JP', sans-serif;
}
.btn-primary:hover { opacity: 0.85; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-secondary {
  display: inline-block;
  border: 1px solid #4A5859;
  color: #4A5859;
  padding: 0.875rem 2rem;
  font-size: 0.8rem;
  letter-spacing: 0.1em;
  border-radius: 2px;
  cursor: pointer;
  background: transparent;
  text-decoration: none;
  transition: all 0.2s;
  font-family: 'Noto Sans JP', sans-serif;
}
.btn-secondary:hover { background: #4A5859; color: #fff; }

.btn-nav {
  background: none;
  border: none;
  color: #8A9899;
  font-size: 0.8rem;
  cursor: pointer;
  letter-spacing: 0.05em;
  font-family: 'Noto Sans JP', sans-serif;
}
.btn-nav:hover { color: #4A5859; }

.skin-type-badge {
  display: inline-block;
  margin: 1.5rem auto;
  padding: 0.75rem 2.5rem;
  border: 1px solid #4A5859;
  border-radius: 100px;
  font-family: 'Noto Serif JP', serif;
  font-size: 1.4rem;
  font-weight: 400;
  color: #4A5859;
  letter-spacing: 0.1em;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1.25rem;
}

.product-card {
  background: #fff;
  border: 1px solid #E8E4DC;
  border-radius: 4px;
  overflow: hidden;
  text-decoration: none;
  display: block;
  transition: transform 0.2s, box-shadow 0.2s;
}
.product-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(74,88,89,0.1);
}

.product-img-wrap {
  aspect-ratio: 4/3;
  background: #F0EDE6;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Transitions */
.slide-fade-enter-active, .slide-fade-leave-active {
  transition: all 0.3s ease;
}
.slide-fade-enter-from {
  opacity: 0;
  transform: translateX(20px);
}
.slide-fade-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}

.result-screen {
  animation: fadeInUp 0.5s ease;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
