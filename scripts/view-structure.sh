#!/bin/bash

# Script pour visualiser l'arborescence du projet et du VPS

echo "=========================================="
echo "ARBORESCENCE DU PROJET VITE ET GOURMAND"
echo "=========================================="
echo ""

# Fonction pour afficher l'arborescence avec indentation
print_tree() {
    local dir="$1"
    local prefix="$2"
    local items=($(ls -A "$dir" 2>/dev/null))
    local count=${#items[@]}
    local i=0
    
    for item in "${items[@]}"; do
        i=$((i+1))
        local pointer="├── "
        local extension="│   "
        
        if [ $i -eq $count ]; then
            pointer="└── "
            extension="    "
        fi
        
        echo "${prefix}${pointer}${item}"
        
        if [ -d "$dir/$item" ] && [ ! -L "$dir/$item" ]; then
            # Exclure certains dossiers volumineux
            if [[ "$item" != "node_modules" ]] && [[ "$item" != "vendor" ]] && [[ "$item" != ".git" ]]; then
                print_tree "$dir/$item" "${prefix}${extension}"
            else
                echo "${prefix}${extension}└── (contenu masqué)"
            fi
        fi
    done
}

echo "Structure du projet:"
echo "/opt/Vite_et_Gourmand_dev/"
print_tree "/opt/Vite_et_Gourmand_dev" ""

echo ""
echo "=========================================="
echo "STATISTIQUES"
echo "=========================================="
echo "Nombre total de fichiers PHP:"
find /opt/Vite_et_Gourmand_dev -name "*.php" -type f | wc -l

echo "Nombre total de fichiers:"
find /opt/Vite_et_Gourmand_dev -type f | wc -l

echo "Nombre total de dossiers:"
find /opt/Vite_et_Gourmand_dev -type d | wc -l

echo ""
echo "Taille totale du projet:"
du -sh /opt/Vite_et_Gourmand_dev

echo ""
echo "=========================================="
echo "STRUCTURE SIMPLIFIÉE"
echo "=========================================="

# Vue simplifiée avec find
find /opt/Vite_et_Gourmand_dev -maxdepth 3 -type d | sed 's|/opt/Vite_et_Gourmand_dev||' | sed 's|/| |g' | sed 's|^|  |'

echo ""
echo "=========================================="
echo "Pour voir le VPS complet, utilisez:"
echo "  sudo ls -laR /"
echo "  sudo find / -type d"
echo "=========================================="
