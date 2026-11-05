---
type: story
tags: [processing, permessi, audit-funzionale, audit-sicurezza-a3]
slug: force-complete-ability
title: "Forza la chiusura e bottoni dell'ordine con abilità dedicate"
status: review
phase: REVIEW
owner: Processing
depends_on: []
related: []
created: 2026-11-05
updated: 2026-11-05
---

# Forza la chiusura e bottoni dell'ordine con abilità dedicate

## Story

Come **responsabile dell'impianto**
voglio che **solo chi ha il permesso possa chiudere un ordine oltre la soglia di perdita di massa**
e che **avvio, chiusura, annullo, input e output richiedano ciascuno un permesso**
così da **sapere che una perdita anomala è stata accettata da chi ne risponde**.

## Perché / evidenze

- Audit funzionale: il toggle "Forza la chiusura" (`CompleteProcessingOrderHeaderAction`) era a disposizione di
  chiunque aprisse il modale; il manuale lo riserva al responsabile.
- Audit sicurezza A3: le header action di Processing (`Start/Complete/Cancel/AddInput/RegisterOutput`) controllavano
  solo lo stato dell'ordine, non un'abilità: chi vede l'ordine lo modifica.

## Soluzione

- `ProcessingOrderPolicy`: `start`, `complete`, `forceComplete`, `cancel`, `addInput`, `registerOutput`
  (permessi `processing_order.{abilità}`), più il trait `DeniesUndeclaredFilamentAbilities`.
- `ProcessingOrderAction::authorizeOrder()` (bottone visibile/eseguibile solo con l'abilità sull'ordine, anche
  come header action di un relation manager) e `ensureAllowed()` (ricontrollo nella callback).
- `CompleteProcessingOrderHeaderAction`: toggle `force` visibile solo con `forceComplete`; `force = true` senza
  abilità → `AuthorizationException`.

## Acceptance criteria

1. Senza permessi nessun bottone dell'ordine è autorizzato.
2. `complete` non concede `forceComplete`.
3. Con `processing_order.forceComplete` il toggle compare e la chiusura forzata passa.
4. La tabella abilità/permessi è nel README.

## Test

`tests/Feature/ProcessingOrderActionAuthorizationTest.php` (gruppi *abilità* e *forza la chiusura*).

## Dev agent record

- Agente: Claude (sessione audit Roosko).
- File: `app/Models/Policies/ProcessingOrderPolicy.php`, `app/Filament/Actions/*.php`, `docs/README.md`.
- Handoff Roosko: `CompleteTreatmentTableAction` (Roosko) espone lo stesso toggle: deve mostrarlo solo con
  `Gate::allows('forceComplete', $order)` e ricontrollarlo nella callback.
