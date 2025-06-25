#!/usr/bin/env python3
"""
Biglia che scivola senza attrito lungo y = |x|^a, x ∈ [-2, 0].

1. Prova tutti gli esponenti a ∈ [-2, 2] con passo 0.1
2. Calcola il tempo di arrivo a (0,0) o assegna np.inf se non arriva
3. Sceglie l'a con tempo minimo e visualizza il moto per quell'a
"""
import numpy as np
import matplotlib
matplotlib.use("TkAgg")          # commenta questa riga se lavori in Jupyter
import matplotlib.pyplot as plt
from matplotlib.animation import FuncAnimation
from scipy.integrate import solve_ivp

# --------------------------------------------------------------------
# Costanti globali
# --------------------------------------------------------------------
g       = 9.81      # m/s²
x0      = -2.0      # posizione di partenza
t_max   = 10.0      # integrazione massima (s)
EPS     = 1e-8      # soglia arrivo (x ~ 0)
Ncurve  = 1000      # punti per disegnare la pista
# --------------------------------------------------------------------

# --------------------------------------------------------------------
# Funzione che restituisce il tempo di arrivo per un dato esponente a
# --------------------------------------------------------------------
def tempo_arrivo(a):
    """
    Ritorna il tempo (s) impiegato dalla biglia a percorrere la pista
    y = |x|^a da x0 a x=0. Se non arriva entro t_max → np.inf.
    Per numeri complessi o errori di integrazione restituisce np.inf.
    """
    # Gestione di casi numericamente critici:
    if not np.isfinite(a):
        return np.inf

    def yprime(x):   # y'(x) = -a * |x|^{a-1}  (x<0)
        return -a * np.abs(x)**(a - 1)

    def ode(t, z):   # z = [x, vx]
        x, vx = z
        ax = -g * yprime(x) / (1.0 + yprime(x)**2)
        return vx, ax

    # Evento di arrivo (x attraversa 0 da sinistra verso destra)
    def hit_zero(t, z):
        return z[0] + EPS
    hit_zero.terminal  = True
    hit_zero.direction = 1

    try:
        sol = solve_ivp(ode, (0, t_max), [x0, 0.0],
                        max_step=0.01, events=hit_zero,
                        rtol=1e-8, atol=1e-10)

        if sol.t_events[0].size:
            return sol.t_events[0][0]   # tempo di arrivo
        else:
            return np.inf               # non è arrivata
    except Exception:                    # problemi numerici
        return np.inf
# --------------------------------------------------------------------

# --------------------------------------------------------------------
# Scansione degli esponenti −2 … 2 con passo 0.1
# --------------------------------------------------------------------
a_values  = np.arange(-2.0, 2.0 + 0.05, 0.1)   # +0.05 per includere 2.0
tempi     = []

print(" a\tTempo [s]")
print("-"*18)
for a in a_values:
    t_arr = tempo_arrivo(a)
    tempi.append(t_arr)
    print(f"{a:+.1f}\t{t_arr if np.isfinite(t_arr) else '∞'}")

tempi     = np.array(tempi)
idx_best  = np.argmin(tempi)           # indice del tempo minimo
a_opt     = a_values[idx_best]
t_opt     = tempi[idx_best]

if not np.isfinite(t_opt):
    raise RuntimeError("Nessun valore di a nell'intervallo porta la biglia a (0,0).")

print("\n➤ Esponente ottimale a ≈ {:.2f}".format(a_opt))
print("➤ Tempo minimo di arrivo ≈ {:.4f} s".format(t_opt))

# --------------------------------------------------------------------
# Reintegrazione completa con a ottimale per visualizzare il moto
# --------------------------------------------------------------------
def yprime_opt(x):
    return -a_opt * np.abs(x)**(a_opt - 1)

def ysecond_opt(x):
    return a_opt * (a_opt - 1) * np.abs(x)**(a_opt - 2)

def ode_opt(t, z):
    x, vx = z
    ax = -g * yprime_opt(x) / (1 + yprime_opt(x)**2)
    return vx, ax

sol = solve_ivp(ode_opt, (0, t_max), [x0, 0.0],
                max_step=0.01,
                events=lambda t, z: z[0] + EPS,
                rtol=1e-8, atol=1e-10, dense_output=True)

t  = sol.t
x  = sol.y[0]
vx = sol.y[1]

y  = np.abs(x)**a_opt
vy = yprime_opt(x) * vx
ax = -g * yprime_opt(x) / (1 + yprime_opt(x)**2)
ay = yprime_opt(x) * ax + ysecond_opt(x) * vx**2

# --------------------------------------------------------------------
# Disegno pista e animazione
# --------------------------------------------------------------------
x_curve = np.linspace(-2.0, 0.0, Ncurve)
y_curve = np.abs(x_curve) ** a_opt

fig, axp = plt.subplots(figsize=(6, 4))
axp.plot(x_curve, y_curve, lw=1, color="k", label=f"y = |x|^{a_opt:.2f}")
ball, = axp.plot([], [], 'o', ms=8, color="tab:red")

axp.set_aspect('equal')
axp.set_xlim(-2.1, 0.1)
axp.set_ylim(-0.1, max(y_curve)*1.05)
axp.set_xlabel("x [m]")
axp.set_ylabel("y [m]")
axp.set_title(f"Biglia su y = |x|^{a_opt:.2f}  (t ≈ {t_opt:.3f} s)")
axp.grid(True)
axp.legend()

def init():
    ball.set_data([], [])
    return (ball,)

def update(i):
    ball.set_data([x[i]], [y[i]])
    return (ball,)

anim = FuncAnimation(fig, update, frames=len(x),
                     init_func=init, interval=20, blit=True)

plt.show()
