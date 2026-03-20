# SHORTCODE UI INTEGRATION - PHASE 1

## Objective
Move the dashboard shortcode from a plain placeholder shell into the approved EPOS visual language inside WordPress.

## Rule
The shortcode implementation must stay inside WordPress.

No separate frontend app.

## Scope for Phase 1
This phase does not reproduce the full approved dashboard yet.

It does this only:
- applies the approved dark UI direction
- creates a sidebar/header/content dashboard shell inside the shortcode
- places live KPI values inside the shell
- places live pipeline and task counts inside the shell

## Why This Step Exists
The approved dashboard HTML is large and includes external CDN assumptions.

Directly pasting the full file into a shortcode without adaptation is risky.

So this phase creates the correct WordPress shortcode shell first.

## Next Phase
After this shell is stable inside WordPress:
- replicate more of the approved layout exactly
- replace more static blocks with live backend data
- continue toward a full approved dashboard rendering
