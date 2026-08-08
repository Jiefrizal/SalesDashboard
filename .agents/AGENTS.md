# AGENTS.md - Sales Dashboard System Rules & Data Analysis Specifications

## 1. Rules for Data Extraction & Spreadsheet Processing

### A. Valid Unit Sale Row Filtering Criteria
When reading STU/Sales sheets from branch spreadsheets, a row is defined as an actual unit sale if and only if it satisfies AT LEAST ONE of the following:
- Non-empty, non-dash Chassis/Engine Number (`NO RANGKA` / `NO MESIN`).
- Non-empty, non-dash Customer Name (`NAMA KONSUMEN`).
- Non-empty, non-dash Leasing / Payment Method (`NAMA LEASING` / `LEASING`).

*Note: Pre-filled row serial numbers (1 to 1000) without actual transaction data must be ignored to prevent false ACV count inflation.*

### B. Standard Leasing / Fincoy Mapping Dictionary
All raw payment strings from spreadsheets must be mapped according to the following canonical keys:

1. **CASH**:
   - `CASH`, `TUNAI`, `DIRECT`, `CASH P`, `CASH S`, `KDS` (Kandis direct cash)
2. **MEGA**:
   - `MEGA`, `MAF` (Mega Auto Finance), `MF`
3. **SOF**:
   - `SOF`, `SUMMIT`, `OTO`, `OTOBAN`, `OTO FINANCE`, `OTO MULTIARTHA` (Summit Oto Finance)
4. **ADIRA**:
   - `ADIRA`
5. **BAF**:
   - `BAF`, `BUSSAN`
6. **IMFI**:
   - `IMFI`, `INDOMOBIL`

### C. POS Penjual & Dealer Classification Rule
When extracting POS Penjual / Sales POS from branch spreadsheets:
- **`DEALER` (Main Dealer Pos)**: Matches raw values `SO1`, `SO 1`, `SO-1`, `RIAU 1`, branch name (e.g. `PEKANBARU`, `SEI PAGAR`, `AIR MOLEK`, `SOREK`, `KANDIS`, `MEDAN`), `D1...`, or blank/dash.
- **Sub-POS Branches**: Matches specific sub-pos branch names (e.g. `RIAU 2`, `GARUDA SAKTI`, `LIPAT KAIN`, `BANGKINANG`, `PETAPAHAN`, `BELILAS`, `PERANAP`, `SUNGAI LALA`, `PADANG LUAS`, `KERINCI`, `UKUI`, `PERAWANG`, `KERINCI KANAN`, `SIAK`, `SABAK AUH`, `PANCUR BATU`).

## 2. Achievement Percentage Formulas (% ACH)

All achievement percentages on dashboards and reports must be calculated against their respective targets:

- **Total Sales % ACH**: $\frac{\text{Actual Total STU (ACV)}}{\text{Target Total}} \times 100\%$
- **Cash Sales % ACH**: $\frac{\text{Actual Cash Sales}}{\text{Target Cash}} \times 100\%$
- **Kredit Sales % ACH**: $\frac{\text{Actual Kredit Sales}}{\text{Target Kredit}} \times 100\%$
- **Fincoy % ACH** (e.g. ADIRA): $\frac{\text{Actual Fincoy Sales}}{\text{Target Fincoy}} \times 100\%$
- **POS STU % ACH**: $\frac{\text{Actual POS STU}}{\text{Target POS}} \times 100\%$

## 3. Data Analysis Errors & Resolution Log

| Error ID | Error Description | Root Cause | Fixed Implementation |
| :--- | :--- | :--- | :--- |
| **ERR-01** | ACV sales count inflated beyond actual sales | Row parser counted blank template rows with pre-filled row numbers | Strictly required valid Chassis, Customer Name, or Leasing method |
| **ERR-02** | Cash & Kredit % ACH miscalculated as share of total | Formula used $\frac{\text{Actual Cash}}{\text{Actual Total}}$ instead of target ratio | Corrected formula to $\frac{\text{Actual Cash}}{\text{Target Cash}} \times 100\%$ |
| **ERR-03** | Fincoy % ACH miscalculated against Kredit total | Formula used $\frac{\text{Fincoy Ach}}{\text{Kredit Total}}$ instead of Fincoy target | Corrected formula to $\frac{\text{Fincoy Ach}}{\text{Fincoy Target}} \times 100\%$ |
| **ERR-04** | OTO, MAF, KDS unmapped or grouped as unknown | Raw text variants (`OTO`, `MAF`, `MF`, `KDS`) were not mapped to canonical keys | Standardized mapping: `OTO` $\to$ `SOF`, `MAF`/`MF` $\to$ `MEGA`, `KDS` $\to$ `CASH` |
| **ERR-05** | POS DEALER STU Count Mismatch | Raw entries `SO1`, `SO 1`, `RIAU 1` were unmapped or not grouped into `DEALER` | Standardized rule: `SO1`/`SO 1`/`RIAU 1`/`D1...` $\to$ `DEALER` (Main Dealer Pos) |
