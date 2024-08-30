#!/bin/bash

# Verifica se um SHA foi fornecido como argumento
if [ -z "$1" ]; then
  echo "Erro: Nenhum SHA foi fornecido."
  echo "Uso: ./increment_version.sh <sha>"
  exit 1
fi

# Atribui o argumento SHA a uma variável
sha=$1

git config --local user.email "costa@elton.schivei.nom.br"
git config --local user.name "Elton Schivei Costa"

# Obtém a última tag no formato de versionamento semântico
last_tag=$(git describe --tags $(git rev-list --tags --max-count=1))

# Remove o "v" da tag para manipular apenas os números
version=${last_tag#v}

# Separa os números de versão em variáveis MAJOR, MINOR e PATCH
IFS='.' read -r major minor patch <<< "$version"

# Incrementa o PATCH
patch=$((patch + 1))

# Verifica se o PATCH atingiu o limite de 99
if [ "$patch" -gt 99 ]; then
    patch=0
    minor=$((minor + 1)) # Incrementa o MINOR
fi

# Verifica se o MINOR atingiu o limite de 99
if [ "$minor" -gt 99 ]; then
    minor=0
    major=$((major + 1)) # Incrementa o MAJOR
fi

# Monta a nova versão
new_version="v$major.$minor.$patch"

# Exibe a nova versão
echo "Nova versão: $new_version"

# Cria uma nova tag anotada com a nova versão e SHA fornecido
git tag $new_version $sha -a -m "Tag $new_version"

# Verifica se a criação da tag foi bem-sucedida
if [ $? -eq 0 ]; then
  # Envia a nova tag para o repositório remoto
  git push origin $new_version

  # Verifica se o push foi bem-sucedido
  if [ $? -eq 0 ]; then
    # Define a saída para o GitHub Actions
    echo "version=$new_version" >> $GITHUB_ENV
    echo "Tag $new_version criada e enviada com sucesso."
  else
    echo "Erro ao enviar a tag para o repositório remoto."
    exit 1
  fi
else
  echo "Erro ao criar a tag."
  exit 1
fi
