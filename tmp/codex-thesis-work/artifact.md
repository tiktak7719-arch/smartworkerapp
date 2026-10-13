# SmartWorker thesis template contract

## Reference

- Retained reference: `C:\Users\Laptop Valley\Downloads\Thesis format.docx`
- SHA-256: `5214a8d1a1c47ed17e84cd48ae3e82b9eae686d90eb0e5ccf7f30d9edb4b3eee`
- Source evidence: 46 A4 portrait sections, 875 body paragraphs, 4 tables, 37 embedded media files, 4 footer parts, `styles.xml`, `numbering.xml`, and `theme1.xml`.
- Rendering: the packaged renderer could not start because no LibreOffice/soffice executable is installed. Structural inspection was completed with `section_audit.py`, `style_lint.py`, Python OOXML inspection, and paragraph/table extraction.

## Visual system to retain

- A4 portrait pages; source horizontal margins are approximately 0.89 in left and 0.69 in right. Main-content vertical margins vary between 0.93–1.33 in top and 1.03 in bottom.
- Cover: vertically spaced, centered title block with prominent project title, `Submitted by`, two student lines, department/institution, and academic session.
- Body: source uses Arial and Segoe UI, with direct formatting over the built-in Normal, Body Text, Heading 1–5, List Paragraph, and Normal (Web) styles. Body copy is justified/left-aligned with modest paragraph spacing; chapter opening headings are large and bold.
- Major flow: cover; declaration/approval sheets; acknowledgement; abstract; contents lists; Chapter 1 introduction; Chapter 2 methodology; Chapter 3 SRS; Chapter 4 design; Chapter 5 implementation; Chapter 6 testing; Chapter 7 conclusion/future work/references.
- Figure/caption pattern: short section heading, explanatory text, centred diagram or evidence image, then a centred `Figure N:` caption. Table pattern: labelled caption above a bordered table.

## Source elements not carried forward

- The reference's Virtual Study Group student identity, supervisor, university, topic-specific prose, tables, diagrams, screenshots, captions, and media are all project-specific and must not survive into the SmartWorker thesis.
- Supervisor, university name, student registration numbers, signatures, and examiner details were not supplied. The corresponding administrative wording is retained only where it can be truthfully generic; signature/registration fields are intentionally left blank.

## Content map for this thesis

1. **Cover and front matter:** title `SmartWorker: Intelligent Job Portal and Recruitment Management System`; authors Shaheen Shah and Hamad Khan; Department of Software Engineering; Session 2022–2026.
2. **Chapters 1–3:** project background, scope, objectives, features, Agile methodology, schedule/deliverables/risk plan, functional and non-functional requirements.
3. **Chapter 4 design:** a code-derived high-level architecture diagram, use-case diagram, and ER/data model diagram. The editable entities are User, UserProfile, JobListing, JobApplication, SavedJob, Conversation, ChatMessage, Rating, SmartNotification, Category, Contact, and BlogPost.
4. **Chapters 5–6:** implementation of Laravel/Blade/Vite/Tailwind components, role-based access, matching, notifications, documents, chat, test evidence from the test suite and observed code behaviour.
5. **Chapter 7:** conclusion, bounded future work, and primary technology references.

## Fidelity and deviation gates

- The final deliverable must have A4 geometry, a centred cover, formal front matter, consistent heading ladder, figure captions, and readable bordered tables derived from the reference.
- Project-specific source media is replaced with new, code-derived architecture/use-case/data-model diagrams. This is intentional and required for factual accuracy.
- No unspecified identity, supervisor, institution, quantitative performance claim, or fabricated screenshot is permitted.
- Final package must retain Word-native styles and be structurally valid. The new content is materially different from the retained reference, so source page/section count and exact pagination are intentionally not preserved.
