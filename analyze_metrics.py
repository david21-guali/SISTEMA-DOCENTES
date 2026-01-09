import json
import os

metrics_path = r'reports/metrics/classes.js'

if not os.path.exists(metrics_path):
    print(f"Error: {metrics_path} not found.")
else:
    with open(metrics_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    json_str = content[content.find('['):content.rfind(']')+1]
    classes = json.loads(json_str)
    
    # Analyze classes with either MI < 65 or CCN > 10
    hotspots = [c for c in classes if c.get('mi', 100) < 65 or c.get('ccn', 0) > 10]
    
    print(f"Total Hotspots Found: {len(hotspots)}")
    print("-" * 50)
    # Sort by MI (ascending)
    for c in sorted(hotspots, key=lambda x: x.get('mi', 100)):
        color = "RED" if c.get('mi', 100) < 65 else "YELLOW"
        print(f"[{color}] MI: {c['mi']:.2f} | CCN: {c['ccn']} | Name: {c['name']}")
