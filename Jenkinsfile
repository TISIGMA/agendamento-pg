pipeline {
    agent any
    environment {
        IMAGE = "agendamento-app"
        STACK = "agendamento-pg"
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
                    | xargs -P4 -I{} sh -c 'docker run --rm \
                        -v "'"${WORKSPACE}"'":/app \
                        php:8.2-cli \
                        php -l "/app/{}" 2>&1' || true
                '''
            }
        }
        stage('Build') {
            steps {
                sh """
                    docker build \
                        -t ${IMAGE}:${BUILD_NUMBER} \
                        -t ${IMAGE}:latest \
                        .
                """
            }
        }
        stage('Deploy') {
            when { expression { env.GIT_BRANCH == 'origin/main' || env.GIT_BRANCH == 'main' } }
            steps {
                sh """
                    docker stack deploy \
                        --compose-file docker-compose.yml \
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
