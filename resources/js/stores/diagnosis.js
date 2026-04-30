import { defineStore } from 'pinia';

export const useDiagnosisStore = defineStore('diagnosis', {
    state: () => ({
        questions: [],
        answers: {},
        currentStep: 0,
        loading: false,
        result: null,
        error: null,
    }),

    getters: {
        totalSteps: (state) => state.questions.length,
        currentQuestion: (state) => state.questions[state.currentStep] ?? null,
        isComplete: (state) => state.currentStep >= state.questions.length && state.questions.length > 0,
        answeredCount: (state) => Object.keys(state.answers).length,
        progressPercent: (state) => {
            if (!state.questions.length) return 0;
            return Math.round((state.currentStep / state.questions.length) * 100);
        },
    },

    actions: {
        async fetchQuestions() {
            this.loading = true;
            try {
                const res = await fetch('/api/diagnosis/questions');
                const data = await res.json();
                this.questions = data.questions;
            } catch (e) {
                this.error = '質問の読み込みに失敗しました。';
            } finally {
                this.loading = false;
            }
        },

        selectAnswer(questionId, score, label) {
            this.answers[questionId] = { score, label };
        },

        nextStep() {
            if (this.currentStep < this.questions.length) {
                this.currentStep++;
            }
        },

        prevStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
            }
        },

        async submitDiagnosis() {
            this.loading = true;
            this.error = null;
            try {
                const answersArray = this.questions.map(q => this.answers[q.id]?.score ?? 0);
                const res = await fetch('/api/diagnosis/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ answers: answersArray }),
                });

                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 422) {
                        const messages = Object.values(data.errors ?? {}).flat();
                        this.error = messages.length > 0
                            ? messages[0]
                            : '入力内容を確認してください。';
                    } else if (res.status === 429) {
                        this.error = '送信回数の制限に達しました。しばらくしてから再度お試しください。';
                    } else {
                        this.error = 'サーバーエラーが発生しました。しばらくしてから再度お試しください。';
                    }
                    return;
                }

                this.result = data;
                if (data.diagnosis_id) {
                    window.location.href = `/diagnosis/result/${data.diagnosis_id}`;
                }
            } catch (e) {
                this.error = 'ネットワークエラーが発生しました。接続を確認してから再度お試しください。';
            } finally {
                this.loading = false;
            }
        },

        reset() {
            this.answers = {};
            this.currentStep = 0;
            this.result = null;
            this.error = null;
        },
    },
});
