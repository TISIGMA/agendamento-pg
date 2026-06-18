pipeline {
    agent any

    environment {
        IMAGE     = "agendamento-app"
        APP_DIR   = "/opt/agendamento-pg"
        STACK     = "agendamento"
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
                        ! -path "./.composer-home/*" \
                    | xargs -P4 -I{} php -l {} \
                    | grep -v "No syntax errors" || true
                '''
            }
        }

        stage('Build') {
            steps {
                sh """
                    docker build \
                        -f docker/Dockerfile \
                        -t ${IMAGE}:${BUILD_NUMBER} \
                        -t ${IMAGE}:latest \
                        .
                """
            }
        }

        stage('Deploy') {
            when { branch 'main' }
            steps {
                sh """
                    cd ${APP_DIR}/docker
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
