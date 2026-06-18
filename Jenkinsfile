pipeline {
    agent any
    environment {
        IMAGE   = "agendamento-app"
        APP_DIR = "/opt/agendamento-pg"
        STACK   = "agendamento-pg"
    }
    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }
        stage('Validar PHP') {
            steps {
                sh '''
                    find . -name "*.php" \
                        ! -path "./app_vendor/*" \
                        ! -path "./vendor/*" \
                    | while read f; do
                        docker run --rm \
                            -v "${WORKSPACE}:/app" \
                            php:8.2-cli \
                            php -l "/app/$f"
                    done || true
                '''
            }
        }
        stage('Build') {
            steps {
                sh """
                    docker build \
                        -f ${APP_DIR}/Dockerfile \
                        -t ${IMAGE}:${BUILD_NUMBER} \
                        -t ${IMAGE}:latest \
                        ${APP_DIR}
                """
            }
        }
        stage('Deploy') {
            when { branch 'main' }
            steps {
                sh """
                    docker stack deploy \
                        --compose-file ${APP_DIR}/docker-compose.yml \
                        --with-registry-auth \
                        --prune \
                        ${STACK}
                """
            }
        }
    }
    post {
        success { echo "✅ Build #${BUILD_NUMBER} publicado na stack '${STACK}'." }
        failure { echo "❌ Falha no build #${BUILD_NUMBER}." }
        always  { cleanWs() }
    }
}
