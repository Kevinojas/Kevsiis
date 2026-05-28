"""Tests para el agente dinero_antes."""

import sys
import os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), ".."))

from dinero_antes import analizar, parse_args


def test_negocio_rentable():
    """Un negocio con buen margen, ventas altas y poca competencia debe ser rentable."""
    datos = {
        "costo": 300,
        "precio": 1500,
        "ventas_mensuales": 500,
        "costos_fijos": 50000,
        "inversion": 200000,
        "competidores": 2,
        "tamano_mercado": 10000,
    }
    r = analizar(datos)
    assert r["score"] >= 80, f"Score {r['score']} debería ser >= 80"
    assert "SÍ GENERA DINERO" in r["decision"]


def test_negocio_no_rentable():
    """Margen negativo debe dar NO GENERA DINERO."""
    datos = {
        "costo": 1000,
        "precio": 800,
        "ventas_mensuales": 100,
        "costos_fijos": 50000,
        "inversion": 500000,
        "competidores": 20,
        "tamano_mercado": 1000,
    }
    r = analizar(datos)
    assert r["score"] < 60, f"Score {r['score']} debería ser < 60"
    assert "NO GENERA DINERO" in r["decision"]


def test_punto_equilibrio():
    """Punto de equilibrio positivo cuando hay margen."""
    datos = {
        "costo": 500,
        "precio": 1000,
        "ventas_mensuales": 200,
        "costos_fijos": 30000,
        "inversion": 100000,
        "competidores": 5,
        "tamano_mercado": 5000,
    }
    r = analizar(datos)
    assert r["punto_eq_unidades"] < float("inf")
    assert r["punto_eq_unidades"] > 0


def test_margen_porcentual():
    """Verificar cálculo de margen porcentual."""
    datos = {
        "costo": 400,
        "precio": 1000,
        "ventas_mensuales": 100,
        "costos_fijos": 20000,
        "inversion": 50000,
        "competidores": 3,
        "tamano_mercado": 2000,
    }
    r = analizar(datos)
    assert abs(r["margen_porcentual"] - 60.0) < 0.01


def test_parse_args():
    """parse_args debe devolver un dict con las claves correctas."""
    argv = [
        "--costo", "500",
        "--precio", "1500",
        "--ventas-mensuales", "200",
        "--costos-fijos", "30000",
        "--inversion", "100000",
        "--competidores", "3",
        "--tamano-mercado", "5000",
    ]
    d = parse_args(argv)
    assert d["costo"] == 500.0
    assert d["precio"] == 1500.0
    assert d["ventas_mensuales"] == 200
    assert d["costos_fijos"] == 30000.0
    assert d["inversion"] == 100000.0
    assert d["competidores"] == 3
    assert d["tamano_mercado"] == 5000
    assert d["nombre"] == "Mi producto"
