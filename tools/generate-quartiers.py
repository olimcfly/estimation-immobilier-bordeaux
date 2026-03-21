#!/usr/bin/env python3
"""
Génère le bloc PHP des quartiers à partir de cities.json
et remplace le contenu dans quartiers.php

Usage: python3 generate-quartiers.py <cities.json> <city_slug> <quartiers.php>
"""

import json
import re
import sys


def main():
    if len(sys.argv) != 4:
        print("Usage: python3 generate-quartiers.py <cities.json> <city_slug> <quartiers.php>")
        sys.exit(1)

    cities_file = sys.argv[1]
    city_slug = sys.argv[2]
    quartiers_file = sys.argv[3]

    with open(cities_file, encoding="utf-8") as f:
        data = json.load(f)

    if city_slug not in data:
        print(f"Ville '{city_slug}' non trouvée dans {cities_file}")
        sys.exit(1)

    city = data[city_slug]
    quartiers = city["quartiers"]

    # Génération du code PHP
    lines = ["$quartiers = ["]
    for q in quartiers:
        lines.append("    [")
        nom = q["nom"].replace("'", "\\'")
        lines.append(f"        'nom' => '{nom}',")
        desc = q["description"].replace('"', '\\"')
        lines.append(f'        \'description\' => "{desc}",')
        lines.append(f"        'prix_m2' => {q['prix_m2']},")
        lines.append(f"        'prix_moyen' => {q['prix_moyen']},")
        carac = "', '".join(q["caracteristiques"])
        lines.append(f"        'caracteristiques' => ['{carac}'],")
        lines.append(f"        'population' => '{q['population']}',")
        trans = q["transports"].replace("'", "\\'")
        lines.append(f"        'transports' => '{trans}',")
        lines.append(f"        'attractivite' => '{q['attractivite']}',")
        lines.append(f"        'coords' => '{q['coords']}',")
        lines.append(f"        'tendance' => '{q['tendance']}',")
        lines.append("    ],")
    lines.append("];")

    new_quartiers = "\n".join(lines)

    # Lecture et remplacement dans quartiers.php
    with open(quartiers_file, "r", encoding="utf-8") as f:
        content = f.read()

    pattern = r"\$quartiers\s*=\s*\[.*?\];"
    content = re.sub(pattern, new_quartiers, content, flags=re.DOTALL)

    with open(quartiers_file, "w", encoding="utf-8") as f:
        f.write(content)

    print(f"Quartiers mis à jour dans {quartiers_file}")


if __name__ == "__main__":
    main()
