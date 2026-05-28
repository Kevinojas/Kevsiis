#!/usr/bin/env python3
"""
dinero_antes.py — Agente de IA para saber si tu producto genera dinero
antes de gastar un peso argentino.

Modo interactivo (recomendado):
    python dinero_antes.py

Modo rápido (argumentos):
    python dinero_antes.py --costo 500 --precio 1500 --ventas-mensuales 200 \
                           --costos-fijos 30000 --inversion 100000
"""

import argparse
import sys
import math


# ──────────────────────────────────────────────
# Entrada de datos
# ──────────────────────────────────────────────

PROMPTS = {
    "nombre": "¿Cómo se llama tu producto? ",
    "costo": "Costo de producir/vender una unidad (en ARS): ",
    "precio": "Precio de venta por unidad (en ARS): ",
    "ventas_mensuales": "Ventas estimadas por mes (unidades): ",
    "costos_fijos": "Costos fijos mensuales (alquiler, sueldos, etc.) en ARS: ",
    "inversion": "Inversión inicial necesaria (ARS): ",
    "competidores": "¿Cuántos competidores directos tenés? ",
    "tamano_mercado": "Tamaño estimado del mercado (cantidad de clientes potenciales): ",
}


def preguntar(msg: str, tipo=float, min_val=0.0) -> float:
    while True:
        raw = input(msg).strip()
        if not raw:
            continue
        try:
            val = tipo(raw)
            if val < min_val:
                print(f"El valor debe ser >= {min_val}.")
                continue
            return val
        except (ValueError, TypeError):
            print("Ingresá un número válido.")


def recolectar_datos() -> dict:
    print("=" * 60)
    print("💰  DINERO ANTES  —  ¿Tu producto genera dinero?")
    print("=" * 60)
    print()

    datos = {}
    datos["nombre"] = input(PROMPTS["nombre"]).strip() or "Mi producto"
    datos["costo"] = preguntar(PROMPTS["costo"], float, 0)
    datos["precio"] = preguntar(PROMPTS["precio"], float, 0)
    datos["ventas_mensuales"] = preguntar(PROMPTS["ventas_mensuales"], int, 0)
    datos["costos_fijos"] = preguntar(PROMPTS["costos_fijos"], float, 0)
    datos["inversion"] = preguntar(PROMPTS["inversion"], float, 0)
    datos["competidores"] = preguntar(PROMPTS["competidores"], int, 0)
    datos["tamano_mercado"] = preguntar(PROMPTS["tamano_mercado"], int, 0)
    return datos


# ──────────────────────────────────────────────
# Motor de análisis
# ──────────────────────────────────────────────

def analizar(datos: dict) -> dict:
    c = datos["costo"]
    p = datos["precio"]
    q = datos["ventas_mensuales"]
    cf = datos["costos_fijos"]
    inv = datos["inversion"]
    comp = datos["competidores"]
    mercado = datos["tamano_mercado"]

    margen_unitario = p - c
    margen_porcentual = (margen_unitario / p * 100) if p > 0 else 0.0
    contribucion_mensual = margen_unitario * q
    ganancia_neta_mensual = contribucion_mensual - cf
    margen_neto_porcentual = (ganancia_neta_mensual / (p * q) * 100) if p * q > 0 else 0.0

    punto_eq_unidades = math.ceil(cf / margen_unitario) if margen_unitario > 0 else float("inf")
    participacion_necesaria = (q / mercado * 100) if mercado > 0 else 0.0

    if ganancia_neta_mensual > 0:
        meses_recupero = math.ceil(inv / ganancia_neta_mensual)
        recupera_en_12m = ganancia_neta_mensual * 12 >= inv
    else:
        meses_recupero = float("inf")
        recupera_en_12m = False

    score = 0.0
    razones = []

    if margen_porcentual >= 40:
        score += 25
    elif margen_porcentual >= 20:
        score += 15
    elif margen_porcentual > 0:
        score += 5
    else:
        razones.append("❌  Margen negativo: vendés a pérdida por unidad.")

    if ganancia_neta_mensual > 0:
        if ganancia_neta_mensual > cf:
            score += 30
        elif ganancia_neta_mensual > cf * 0.5:
            score += 20
        else:
            score += 10
    else:
        razones.append("❌  Ganancia neta mensual negativa o nula.")

    if meses_recupero <= 6:
        score += 20
    elif meses_recupero <= 12:
        score += 15
    elif meses_recupero <= 24:
        score += 5
    else:
        razones.append("❌  La inversión no se recupera en menos de 2 años.")

    if comp <= 3:
        score += 10
    elif comp <= 10:
        score += 5
    else:
        razones.append("⚠️  Mercado muy competitivo.")

    if participacion_necesaria <= 1:
        score += 15
    elif participacion_necesaria <= 5:
        score += 10
    elif participacion_necesaria <= 15:
        score += 5
    else:
        razones.append("⚠️  Necesitás una porción de mercado muy alta.")

    if score >= 80:
        decision = "SÍ GENERA DINERO ✅"
        confianza = "ALTA"
    elif score >= 60:
        decision = "POSIBLE — pero con riesgos ⚠️"
        confianza = "MEDIA"
    else:
        decision = "NO GENERA DINERO ❌"
        confianza = "BAJA"

    return {
        "margen_unitario": margen_unitario,
        "margen_porcentual": margen_porcentual,
        "contribucion_mensual": contribucion_mensual,
        "ganancia_neta_mensual": ganancia_neta_mensual,
        "margen_neto_porcentual": margen_neto_porcentual,
        "punto_eq_unidades": punto_eq_unidades,
        "participacion_necesaria": participacion_necesaria,
        "meses_recupero": meses_recupero,
        "recupera_en_12m": recupera_en_12m,
        "score": round(score, 1),
        "decision": decision,
        "confianza": confianza,
        "razones": razones,
    }


# ──────────────────────────────────────────────
# Reporte / Salida
# ──────────────────────────────────────────────

def formatear(resultado: dict, datos: dict, nombre: str) -> str:
    r = resultado
    d = datos
    line = "─" * 60

    out = [
        line,
        f"  📊  ANÁLISIS: {nombre}",
        line,
        "",
        "  ┌─ Métricos clave ──────────────────────────────",
        f"  │  Precio venta         —  ${d['precio']:.0f}",
        f"  │  Costo unitario       —  ${d['costo']:.0f}",
        f"  │  Margen unitario      —  ${r['margen_unitario']:.0f}  ({r['margen_porcentual']:.1f}%)",
        f"  │  Ventas mensuales     —  {d['ventas_mensuales']} unidades",
        f"  │  Contribución mensual —  ${r['contribucion_mensual']:.0f}",
        f"  │  Costos fijos/mes     —  ${d['costos_fijos']:.0f}",
        f"  │  Ganancia neta/mes    —  ${r['ganancia_neta_mensual']:.0f}  ({r['margen_neto_porcentual']:.1f}%)",
        f"  │  Inversión inicial    —  ${d['inversion']:.0f}",
        "  └────────────────────────────────────────────────",
        "",
        "  ┌─ Indicadores ─────────────────────────────────",
    ]

    if r["punto_eq_unidades"] == float("inf"):
        out.append("  │  Punto equilibrio     —  Nunca (margen negativo)")
    else:
        out.append(f"  │  Punto equilibrio     —  {r['punto_eq_unidades']} unid./mes")

    if r["meses_recupero"] == float("inf"):
        out.append("  │  Recupero inversión   —  No se recupera")
    else:
        out.append(f"  │  Recupero inversión   —  ~{r['meses_recupero']} meses")

    out.append(f"  │  Participación merc.  —  {r['participacion_necesaria']:.1f}% del mercado")
    out.append(f"  │  Score de viabilidad  —  {r['score']}/100")
    out.append("  └────────────────────────────────────────────────")
    out.append("")

    out.append(f"  🏆  DECISIÓN: {r['decision']}")
    out.append(f"  📈  Confianza: {r['confianza']}")
    out.append("")

    if r["razones"]:
        out.append("  ⚠️  Advertencias:")
        for razon in r["razones"]:
            out.append(f"     {razon}")
        out.append("")

    out.append("  💡  Tip: Si el resultado es negativo, ajustá precio,")
    out.append("       reducí costos o validá con clientes reales antes de invertir.")
    out.append(line)
    return "\n".join(out)


# ──────────────────────────────────────────────
# CLI
# ──────────────────────────────────────────────

def parse_args(argv: list[str]) -> dict:
    parser = argparse.ArgumentParser(
        description="Dinero Antes — ¿Tu producto genera dinero?",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="Ejemplo:\n  python dinero_antes.py --costo 500 --precio 1500 --ventas-mensuales 200 --costos-fijos 30000 --inversion 100000",
    )
    parser.add_argument("--nombre", default="Mi producto", help="Nombre del producto")
    parser.add_argument("--costo", type=float, required=True, help="Costo unitario (ARS)")
    parser.add_argument("--precio", type=float, required=True, help="Precio de venta (ARS)")
    parser.add_argument("--ventas-mensuales", type=int, required=True, help="Ventas estimadas por mes")
    parser.add_argument("--costos-fijos", type=float, required=True, help="Costos fijos mensuales (ARS)")
    parser.add_argument("--inversion", type=float, required=True, help="Inversión inicial (ARS)")
    parser.add_argument("--competidores", type=int, default=0, help="Cant. de competidores directos")
    parser.add_argument("--tamano-mercado", type=int, default=1000, help="Tamaño del mercado (clientes potenciales)")
    return vars(parser.parse_args(argv))


def main():
    if len(sys.argv) > 1:
        datos = parse_args(sys.argv[1:])
    else:
        datos = recolectar_datos()

    resultado = analizar(datos)
    print()
    print(formatear(resultado, datos, datos.get("nombre", "Mi producto")))


if __name__ == "__main__":
    main()
