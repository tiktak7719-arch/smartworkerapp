from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle,
    PageBreak, KeepTogether
)

OUT = r"D:\My Web Sites\smartworkerapp\output\pdf\SmartWorker_Viva_Guide_Roman_Urdu.pdf"

NAVY = colors.HexColor("#12355B")
BLUE = colors.HexColor("#1D6FA5")
TEAL = colors.HexColor("#0E8A8A")
PALE = colors.HexColor("#EDF4F8")
PALE_TEAL = colors.HexColor("#E8F5F3")
INK = colors.HexColor("#17212B")
MUTED = colors.HexColor("#53616E")
LINE = colors.HexColor("#CCD6DF")

styles = getSampleStyleSheet()
styles.add(ParagraphStyle(
    name="CoverTitle", parent=styles["Title"], fontName="Helvetica-Bold",
    fontSize=27, leading=32, textColor=NAVY, alignment=TA_CENTER, spaceAfter=8,
))
styles.add(ParagraphStyle(
    name="CoverSub", parent=styles["Normal"], fontName="Helvetica",
    fontSize=13, leading=18, textColor=MUTED, alignment=TA_CENTER,
))
styles.add(ParagraphStyle(
    name="H1Viva", parent=styles["Heading1"], fontName="Helvetica-Bold",
    fontSize=17, leading=22, textColor=NAVY, spaceBefore=7, spaceAfter=10,
    keepWithNext=True,
))
styles.add(ParagraphStyle(
    name="H2Viva", parent=styles["Heading2"], fontName="Helvetica-Bold",
    fontSize=12.2, leading=16, textColor=BLUE, spaceBefore=8, spaceAfter=5,
    keepWithNext=True,
))
styles.add(ParagraphStyle(
    name="BodyViva", parent=styles["BodyText"], fontName="Helvetica",
    fontSize=9.6, leading=13.3, textColor=INK, spaceAfter=5,
))
styles.add(ParagraphStyle(
    name="BulletViva", parent=styles["BodyText"], fontName="Helvetica",
    fontSize=9.35, leading=13, textColor=INK, leftIndent=13, firstLineIndent=-8,
    spaceAfter=3,
))
styles.add(ParagraphStyle(
    name="Q", parent=styles["BodyText"], fontName="Helvetica-Bold",
    fontSize=9.8, leading=13.4, textColor=NAVY, spaceBefore=4, spaceAfter=2,
    keepWithNext=True,
))
styles.add(ParagraphStyle(
    name="A", parent=styles["BodyText"], fontName="Helvetica",
    fontSize=9.35, leading=13.1, textColor=INK, leftIndent=0, spaceAfter=5,
))
styles.add(ParagraphStyle(
    name="Small", parent=styles["BodyText"], fontName="Helvetica",
    fontSize=8.4, leading=11.2, textColor=MUTED, spaceAfter=3,
))
styles.add(ParagraphStyle(
    name="Tiny", parent=styles["BodyText"], fontName="Helvetica",
    fontSize=7.8, leading=10.2, textColor=MUTED, alignment=TA_CENTER,
))


def p(text, style="BodyViva"):
    return Paragraph(text, styles[style])


def bullet(text):
    return p("- " + text, "BulletViva")


def qa(question, answer):
    return [p("Sawal: " + question, "Q"), p("Jawab: " + answer, "A")]


def section(title):
    return p(title, "H1Viva")


def sub(title):
    return p(title, "H2Viva")


def table(data, widths, header=True, font_size=8.35):
    converted = []
    for row in data:
        converted.append([cell if hasattr(cell, "wrap") else p(str(cell), "Small") for cell in row])
    t = Table(converted, colWidths=widths, repeatRows=1 if header else 0, hAlign="LEFT")
    rules = [
        ("GRID", (0, 0), (-1, -1), 0.35, LINE),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("LEFTPADDING", (0, 0), (-1, -1), 6),
        ("RIGHTPADDING", (0, 0), (-1, -1), 6),
        ("TOPPADDING", (0, 0), (-1, -1), 5),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
        ("FONTSIZE", (0, 0), (-1, -1), font_size),
    ]
    if header:
        rules.extend([
            ("BACKGROUND", (0, 0), (-1, 0), NAVY),
            ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
            ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
        ])
        for c in range(len(data[0])):
            converted[0][c] = Paragraph(str(data[0][c]), ParagraphStyle(
                "TH" + str(c), parent=styles["Small"], fontName="Helvetica-Bold",
                fontSize=8.2, leading=10.3, textColor=colors.white
            ))
        t._cellvalues[0] = converted[0]
        for r in range(1, len(data)):
            if r % 2 == 0:
                rules.append(("BACKGROUND", (0, r), (-1, r), PALE))
    t.setStyle(TableStyle(rules))
    return t


def page_number(canvas, doc):
    canvas.saveState()
    w, h = A4
    canvas.setStrokeColor(LINE)
    canvas.setLineWidth(0.45)
    canvas.line(doc.leftMargin, 1.35 * cm, w - doc.rightMargin, 1.35 * cm)
    canvas.setFont("Helvetica", 8)
    canvas.setFillColor(MUTED)
    canvas.drawString(doc.leftMargin, 0.85 * cm, "SmartWorker Viva Guide - Roman Urdu")
    canvas.drawRightString(w - doc.rightMargin, 0.85 * cm, "Page %d" % doc.page)
    canvas.restoreState()


def cover_footer(canvas, doc):
    canvas.saveState()
    w, h = A4
    canvas.setFillColor(NAVY)
    canvas.rect(0, h - 1.2 * cm, w, 1.2 * cm, fill=1, stroke=0)
    canvas.setFillColor(TEAL)
    canvas.rect(0, 0, w, 0.7 * cm, fill=1, stroke=0)
    canvas.restoreState()


doc = SimpleDocTemplate(
    OUT, pagesize=A4, rightMargin=1.55 * cm, leftMargin=1.55 * cm,
    topMargin=1.75 * cm, bottomMargin=1.75 * cm,
    title="SmartWorker Viva Guide Roman Urdu", author="SmartWorker Project Team",
)

story = []

# Cover
story.extend([
    Spacer(1, 3.35 * cm),
    p("SmartWorker", "CoverTitle"),
    p("Viva Guide - Roman Urdu", "CoverTitle"),
    Spacer(1, 0.25 * cm),
    p("Laravel Based Job Portal", "CoverSub"),
    p("Print copy for project explanation and viva preparation", "CoverSub"),
    Spacer(1, 1.15 * cm),
])
cover_table = Table([
    [p("<b>Is guide ko kaise use karna hai</b><br/>Pehle page 2 ka opening script 2 ya 3 dafa bol kar practice karein. Phir flow, matching aur security ko samajh lein. Aakhir mein Q and A ko revise karein. Jawab ratta lagane ke bajaye apne project ke flow ke saath samjhayen.", "BodyViva")],
], colWidths=[15.3 * cm])
cover_table.setStyle(TableStyle([
    ("BACKGROUND", (0, 0), (-1, -1), PALE_TEAL),
    ("BOX", (0, 0), (-1, -1), 0.6, TEAL),
    ("LEFTPADDING", (0, 0), (-1, -1), 14),
    ("RIGHTPADDING", (0, 0), (-1, -1), 14),
    ("TOPPADDING", (0, 0), (-1, -1), 13),
    ("BOTTOMPADDING", (0, 0), (-1, -1), 13),
]))
story += [cover_table, Spacer(1, 1.1 * cm), p("Important: Jahan guide mein 'main ne' likha ho, agar project team mein bana hai to 'hum ne' bol dein.", "Tiny"), PageBreak()]

# Page 2: opening + purpose
story.append(section("1. Viva ka Opening Script"))
story.append(p("Assalam-o-Alaikum. Mera project SmartWorker hai. Ye Laravel-based web job portal hai jo candidates, employers aur administrators ko ek hi platform par connect karta hai. Is ka maqsad recruitment process ko organised, transparent aur secure banana hai.", "BodyViva"))
story.append(p("Is system mein candidate profile aur skills maintain karta hai, jobs search karta hai, job save karta hai aur CV plus cover letter ke sath apply karta hai. Employer apni company profile aur job listings manage karta hai, applications review karta hai aur status update karta hai. Admin users, jobs, contact messages aur overall activity moderate karta hai.", "BodyViva"))
story.append(p("SmartWorker ki extra value explainable matching hai. System skills, location aur rating ko use karke relevant jobs aur candidates recommend karta hai. Sath notifications, chat, ratings aur protected document downloads bhi hain. Architecture Laravel MVC par based hai, is liye code organised aur maintainable rehta hai.", "BodyViva"))
story.append(sub("30 second mein project ka flow"))
story += [
    bullet("User register karta hai aur role choose karta hai: candidate ya employer."),
    bullet("Candidate profile, skills aur resume add karta hai; employer company data aur job post karta hai."),
    bullet("Candidate active job browse karke apply karta hai. Application record database mein save hota hai."),
    bullet("Employer us application ko review karke status update karta hai. Candidate ko notification milti hai."),
    bullet("Matching service skills, location aur ratings se suggestions nikalti hai. Admin platform ko supervise karta hai."),
]
story.append(sub("Problem statement"))
story.append(p("Local aur small-scale hiring mein vacancies aksar social media, notices, emails aur calls mein scattered hoti hain. Candidate ko relevant job aur application status track karna mushkil hota hai, jabke employer ko CVs aur hiring decisions organise karne mein problem hoti hai. SmartWorker in tamam steps ko ek central web platform mein arrange karta hai.", "BodyViva"))
story.append(sub("Project objectives"))
story += [
    bullet("Centralised job discovery aur job application platform dena."),
    bullet("Employer ko jobs create, update, activate aur manage karne dena."),
    bullet("Profiles, skills, resumes, applications aur statuses ko organised rakhna."),
    bullet("Candidate, employer aur admin ke liye controlled role-based access dena."),
    bullet("Skills, location aur rating ke basis par transparent recommendations dena."),
    bullet("Notifications, chat aur ratings se communication improve karna."),
]
story.append(PageBreak())

# Page 3: modules / technology
story.append(section("2. Main Modules aur Users"))
story.append(table([
    ["Role", "Main kaam", "Viva mein key point"],
    ["Candidate", "Profile, skills, resume, job search, saved jobs, apply, application history, notifications aur chat.", "Candidate sirf candidate routes access karta hai."],
    ["Employer", "Company profile, job posting, job management, applicant review, status update, candidate matches aur chat.", "Employer apni jobs aur unki applications manage karta hai."],
    ["Administrator", "Users, jobs, contact messages, applications aur verification/moderation.", "Admin ka route group separate aur protected hai."],
], [2.5 * cm, 8.0 * cm, 5.0 * cm]))
story.append(sub("Technology stack"))
story.append(table([
    ["Layer", "Technology", "Use"],
    ["Backend", "PHP 8.3 and Laravel 13", "Routes, controllers, validation, authentication, middleware and business logic."],
    ["Pattern", "MVC", "Model data handle karta hai, View UI dikhata hai, Controller request workflow control karta hai."],
    ["Database", "Relational database via Eloquent ORM; Render deployment mein PostgreSQL", "Users, jobs, applications, profiles, notifications, chats aur ratings store hote hain."],
    ["Frontend", "Blade templates, Vite and CSS assets", "Responsive pages, forms, dashboards aur navigation."],
    ["Testing", "Laravel feature tests and PHPUnit", "Authentication, role access, posting aur application workflow test hota hai."],
    ["Deployment", "Docker and Render configuration", "Demo web service aur PostgreSQL deployment support."],
], [2.3 * cm, 4.5 * cm, 8.7 * cm]))
story.append(sub("MVC request flow"))
story.append(table([
    ["Step", "Flow"],
    ["1", "Browser form/request bhejta hai."],
    ["2", "Named route request ko relevant controller method tak bhejta hai."],
    ["3", "Middleware user login, active account aur role permission check karta hai."],
    ["4", "Form Request/controller input validate karta hai; Eloquent model database se data read/write karta hai."],
    ["5", "Matching ya notification service zarurat par call hoti hai."],
    ["6", "Blade view response ko user-facing page mein render karta hai."],
], [1.4 * cm, 14.1 * cm]))
story.append(p("Short line yaad rakhein: <b>Route request ko controller tak lata hai, controller rules apply karta hai, model database se kaam karta hai aur Blade view final screen show karta hai.</b>", "BodyViva"))
story.append(PageBreak())

# Page 4 data design
story.append(section("3. Database aur Important Relationships"))
story.append(p("Data model User entity ke around design hua hai. Har relationship foreign keys se linked hai, jisse record ownership clear rehti hai aur recruitment data organised rehta hai.", "BodyViva"))
story.append(table([
    ["Table / Model", "Relation / purpose", "Viva answer"],
    ["users", "Role, account status, location, coordinates and profile identity.", "Har account ka central record users table mein hai."],
    ["user_profiles", "One user ka candidate profile ya employer company information.", "User se one-to-one profile relation hai."],
    ["job_listings", "Employer ki job, category, details, active flag, views and coordinates.", "Ek employer ki many job listings ho sakti hain."],
    ["job_applications", "Candidate aur job ke darmiyan application; CV, cover letter, status and notes.", "Unique job_listing_id plus user_id se same job par duplicate application nahi hoti."],
    ["saved_jobs", "Candidate ki bookmarked jobs.", "Unique user_id plus job_listing_id duplicate save ko rokta hai."],
    ["conversations / chat_messages", "Candidate aur employer ke job-related messages.", "Conversation ke multiple messages ho sakte hain."],
    ["smart_notifications", "Job matches aur application updates.", "Read status read_at se track hota hai."],
    ["ratings", "Application ke baad user rating and comment.", "Rating history matching reputation factor mein use hoti hai."],
], [3.1 * cm, 7.5 * cm, 4.9 * cm]))
story.append(sub("Application workflow - exactly kya hota hai"))
story += [
    bullet("Candidate active job ki details dekhta hai aur CV/cover letter submit karta hai."),
    bullet("JobApplication record candidate, job listing aur status ko link karta hai. Default status pending hota hai."),
    bullet("Employer ko new application notification milti hai."),
    bullet("Employer reviewing, shortlisted, rejected ya hired status update kar sakta hai aur note add kar sakta hai."),
    bullet("Status ya note change hone par candidate ko notification create hoti hai."),
    bullet("Candidate application history mein result dekh sakta hai."),
]
story.append(sub("Route protection - short explanation"))
story.append(p("Candidate-only routes auth + role:candidate se protected hain. Employer functions auth + role:employer,admin group mein hain. Admin routes auth + role:admin ke under hain. Middleware sirf role nahi, active account bhi check karta hai. Is tarah candidate employer ka job-post form ya employer candidate ka saved-jobs area access nahi kar sakta.", "BodyViva"))
story.append(PageBreak())

# Page 5 matching
story.append(section("4. Smart Matching aur Notifications"))
story.append(p("Project ka matching feature machine learning model nahi hai. Ye ek <b>explainable rule-based score</b> hai. Is ka faida ye hai ke score ka reason easily explain ho sakta hai aur future mein ML API se replace bhi kiya ja sakta hai.", "BodyViva"))
story.append(table([
    ["Recommendation", "Skills", "Location", "Rating", "Threshold / result"],
    ["Job for candidate", "60%", "30%", "10%", "Score 20% ya zyada ho to list mein aa sakta hai; top 6 returned."],
    ["Candidate for employer", "65%", "25%", "10%", "Score 20% ya zyada ho to list mein aa sakta hai; top 20 returned."],
], [3.5 * cm, 2.0 * cm, 2.2 * cm, 1.8 * cm, 6.0 * cm]))
story.append(sub("Matching score ko samjhayen"))
story += [
    bullet("Skills: candidate ki comma-separated skills ko lowercase/normalise karke job title, tags, requirements aur description mein search kiya jata hai."),
    bullet("Location: remote job ko full location score milta hai. Coordinates available hon to Haversine formula se distance kilometres mein calculate hoti hai."),
    bullet("Distance bands: 10 km tak full score; 30 km tak 80%; 60 km tak 55%; 120 km tak 25%; us se zyada 0."),
    bullet("Coordinates na hon to normalised text location compare hoti hai. Matching city string milne par partial location score milta hai."),
    bullet("Rating: rating score maximum 10 points hai. Agar rating history na ho to predicted rating default 3.0 hai."),
]
story.append(sub("Haversine formula ka simple viva answer"))
story.append(p("Latitude aur longitude earth ki curved surface par points show karte hain. Haversine formula in dono coordinates ke darmiyan approximate great-circle distance nikalta hai. Project mein earth radius 6371 km use hua hai. Is distance se system candidate ke near jobs ya employer ke near candidates ko zyada relevance deta hai.", "BodyViva"))
story.append(sub("Notifications ka flow"))
story += [
    bullet("Employer job post karta hai to MatchingService relevant candidates calculate karti hai."),
    bullet("Job alerts enabled hon aur score 45% ya zyada ho to candidate ke liye job-match notification create hoti hai."),
    bullet("Candidate apply kare to employer ko new-application notification milti hai."),
    bullet("Employer status ya note update kare to candidate ko application-status notification milti hai."),
    bullet("Notifications table mein title, body, URL, data aur read_at save hote hain."),
]
story.append(PageBreak())

# Page 6 security/testing
story.append(section("5. Security, Validation aur Testing"))
story.append(sub("Security controls"))
story.append(table([
    ["Control", "Implementation", "Why important"],
    ["Authentication", "Laravel login, password reset and hashed passwords.", "Unauthorised user protected account actions nahi kar sakta."],
    ["Role middleware", "Candidate, employer and admin route groups with active-account check.", "Har role sirf authorised modules access karta hai."],
    ["Validation", "Form Requests validate registration, jobs, profiles, URLs, salary, file types and other inputs.", "Invalid ya unsafe data database mein jane se pehle reject hota hai."],
    ["CSRF protection", "Laravel web forms include CSRF protection.", "Forged form submissions ke risk ko reduce karta hai."],
    ["Private documents", "Resume/CV authorised controller action se download hota hai; applicant, owning employer ya admin check hota hai.", "Sensitive recruitment files public URL par directly exposed nahi hote."],
    ["Security headers", "nosniff, SAMEORIGIN frame policy, referrer policy, permissions policy and COOP.", "Browser-side protection harden hoti hai."],
], [3.2 * cm, 7.1 * cm, 5.2 * cm]))
story.append(sub("Validation examples"))
story += [
    bullet("Registration: email uniqueness aur password confirmation."),
    bullet("Job: required title, description, job type, URLs, salary range aur optional location coordinates."),
    bullet("Documents: resume/CV PDF, DOC ya DOCX type restrictions ke sath."),
    bullet("Application: one candidate ek job par ek hi application create kar sakta hai - database unique constraint."),
]
story.append(sub("Testing approach"))
story.append(p("Laravel feature tests se real user workflow simulate kiya gaya: register, login, employer posting, candidate/employer role restriction, application creation, notification creation aur application status update. Is se sirf function ka output nahi, balki route se database tak end-to-end behaviour verify hota hai.", "BodyViva"))
story.append(p("Thesis test snapshot mein 13 tests registered thay: 5 passed, 8 skipped aur 10 assertions report huay. Skipped tests ka reason environment mein PDO SQLite extension ki availability thi. Is ko limitation samajh kar honest jawab dena behtar hai; deployment se pehle compatible test database/environment configure karna next step hai.", "BodyViva"))
story.append(sub("Agar examiner pooche: testing ki importance kya hai?"))
story.append(p("Testing se confirm hota hai ke candidate employer-only pages access nahi kar sakta, employer job post kar sakta hai, candidate application create kar sakta hai aur notifications correct user ko ja rahi hain. Ye regressions ko pakarne ka practical tareeqa hai jab future mein code change ho.", "BodyViva"))
story.append(PageBreak())

# Page 7 QAs 1
story.append(section("6. Common Viva Questions - Part 1"))
for question, answer in [
    ("SmartWorker kya hai?", "SmartWorker ek Laravel-based job portal aur recruitment management system hai. Ye candidate, employer aur admin workflows ko ek platform par organise karta hai."),
    ("Is project ki real problem kya solve hoti hai?", "Jobs, CVs aur communication scattered channels mein hotay hain. System job discovery, applications, status tracking aur employer review ko centralise karta hai."),
    ("Aap ne Laravel kyun choose kiya?", "Laravel mein MVC structure, routing, Eloquent ORM, validation, middleware, authentication aur migrations built-in ecosystem ke sath milte hain. Is se secure aur maintainable web app banana easier hota hai."),
    ("MVC kya hota hai?", "Model database/data logic represent karta hai, View user interface show karta hai, aur Controller request ko handle karke Model aur View ke darmiyan coordination karta hai."),
    ("Eloquent ORM kya karta hai?", "Eloquent Laravel ka ORM hai. Is se PHP models ke zariye database tables aur relations ko manage karte hain, raw SQL har jagah likhne ki zarurat kam ho jati hai."),
    ("Migration kya hoti hai?", "Migration database schema ka version-controlled PHP file hoti hai. Is se team ya deployment par same tables, columns, indexes aur foreign keys create kiye ja sakte hain."),
    ("Candidate aur employer ka access kaise separate hai?", "Routes middleware groups mein hain. Candidate-only routes role:candidate ke under hain, employer routes role:employer,admin ke under, aur admin routes dedicated role:admin group mein hain."),
    ("Application duplicate kaise roki?", "job_applications table mein job_listing_id aur user_id ka unique composite constraint hai. Is se ek candidate same job par do records create nahi kar sakta."),
    ("Job ko active/inactive kyun rakha hai?", "Employer ya admin vacancy ko deactivate kar sakta hai. Public job browse aur application flow active jobs par focus karta hai, is se closed jobs ka issue control hota hai."),
    ("Slug kya hai aur use kyun kiya?", "Slug readable URL identifier hai, jaise job title aur company se bana hua unique text. Is se URL clean hota hai aur job detail route user-friendly banta hai."),
]:
    story += qa(question, answer)
story.append(PageBreak())

# Page 8 QAs 2
story.append(section("7. Common Viva Questions - Part 2"))
for question, answer in [
    ("Matching algorithm ka formula kya hai?", "Job recommendation mein skills 60%, location 30%, rating 10% hain. Candidate recommendation mein skills 65%, location 25%, rating 10% hain. Final score 100 se zyada nahi hota."),
    ("Kya aapka system Artificial Intelligence use karta hai?", "Current implementation rule-based explainable recommendation use karti hai, full machine learning model nahi. Is design ko future mein evaluated ML API se replace kiya ja sakta hai."),
    ("Location matching kaise hoti hai?", "Coordinates hon to Haversine formula distance calculate karta hai aur near candidates/jobs ko higher score milta hai. Remote jobs ko full location score milta hai. Coordinates na hon to location text compare hota hai."),
    ("Rating prediction kaise hoti hai?", "Existing ratings ka average aur review comments ka simple positive/negative keyword sentiment use hota hai. Agar history nahi hai to default predicted rating 3.0 hai."),
    ("Notification system kya karta hai?", "New matching job, new application aur application status change par notification record create hota hai. User notifications page par unhein dekh aur read mark kar sakta hai."),
    ("Chat privacy kaise maintain hoti hai?", "Conversation candidate aur employer IDs ke sath linked hoti hai. Authenticated shared routes aur conversation checks ensure karte hain ke relevant parties hi messages dekhain."),
    ("Resume security kaise handle ki?", "Resume/CV protected download controller se serve hota hai. Access applicant/candidate, job-owning employer ya admin ka check karke diya jata hai; private storage preferred hai."),
    ("CSRF kya hota hai?", "Cross-Site Request Forgery mein malicious site user ke session se unwanted request bhejne ki koshish karti hai. Laravel ke CSRF tokens is risk ko mitigate karte hain."),
    ("Feature tests aur unit tests mein farq?", "Feature test route, middleware, controller aur database jaisa complete workflow test karta hai. Unit test chhote isolated method/class ko test karta hai."),
    ("Project ki limitations kya hain?", "Matching keyword-based hai aur test environment mein kuch tests SQLite extension issue ki wajah se skipped thay. Large scale ke liye queues, load testing, audit logs aur stronger analytics add karni hongi."),
    ("Future scope kya hai?", "Evaluated ML matching, mobile apps through secured API, hiring analytics, queue-based high volume notifications, broader security/load/accessibility testing, audit logs aur data retention controls."),
]:
    story += qa(question, answer)
story.append(PageBreak())

# Page 9 demo
story.append(section("8. Live Demo Dene ka Best Order"))
story.append(p("Agar viva mein app khol kar demo dena ho to screens ko random na kholen. Neeche wala order follow karein; is se examiner ko end-to-end story samajh aati hai.", "BodyViva"))
story.append(table([
    ["Order", "Screen / action", "Aap kya bolain"],
    ["1", "Home aur Browse Jobs", "Public user jobs, categories aur filters se opportunities discover kar sakta hai."],
    ["2", "Register / Login", "Registration ke waqt role choose hota hai, aur login ke baad role-appropriate dashboard milta hai."],
    ["3", "Candidate Profile", "Candidate skills, education, experience, links aur resume maintain karta hai; ye matching ke input hain."],
    ["4", "Job Details / Apply", "Candidate active job ki details dekh kar CV aur cover letter ke sath apply karta hai."],
    ["5", "My Applications / Notifications", "Candidate application status aur alerts track karta hai."],
    ["6", "Employer Dashboard / Post Job", "Employer company info ke sath job create aur manage karta hai."],
    ["7", "Manage Applications", "Employer applications review karke status aur notes update karta hai; candidate notify hota hai."],
    ["8", "Job Matches / Chat", "Matching suggestions skills, location aur rating se aati hain; chat follow-up communication ke liye hai."],
    ["9", "Admin Dashboard", "Admin users, jobs, contact messages aur overall application activity supervise karta hai."],
], [1.3 * cm, 5.0 * cm, 9.0 * cm]))
story.append(sub("Viva mein confidence ke 6 rules"))
story += [
    bullet("Har feature ke sath user role aur benefit bhi batayen."),
    bullet("'AI' tabhi bolain jab clear kar dein ke current version explainable rule-based matching hai, black-box ML nahi."),
    bullet("Security mein sirf password hashing na bolain; role middleware, validation, CSRF aur private documents bhi mention karein."),
    bullet("Agar exact code line yaad na ho to workflow explain karein: route -> middleware -> controller -> model -> view."),
    bullet("Limitations ko hide na karein; un ke sath practical future solution batayen."),
    bullet("Short answer dein, phir examiner pooche to detail mein jayein."),
]
story.append(sub("One-line conclusion"))
story.append(p("SmartWorker recruitment ko ek organised, role-based aur secure digital workflow mein convert karta hai, jahan candidate opportunities aur progress manage karta hai, employer hiring manage karta hai, aur admin platform ko moderate karta hai.", "BodyViva"))
story.append(PageBreak())

# Final revision
story.append(section("9. Last Minute Revision Sheet"))
story.append(sub("Ye points zaroor yaad rakhein"))
story += [
    bullet("Project type: Laravel MVC job portal / recruitment management system."),
    bullet("Roles: candidate, employer, administrator."),
    bullet("Core flow: register -> profile/job post -> browse/apply -> review/status -> notification."),
    bullet("Important tables: users, user_profiles, job_listings, job_applications, saved_jobs, conversations, chat_messages, smart_notifications, ratings."),
    bullet("Candidate job matching: 60 skills + 30 location + 10 rating."),
    bullet("Employer candidate matching: 65 skills + 25 location + 10 rating."),
    bullet("Haversine: latitude/longitude se kilometres mein distance."),
    bullet("Security: password hashing, CSRF, Form Request validation, role middleware, active account check, protected file download, headers."),
    bullet("Testing: Laravel feature tests; role access, authentication, job posting, application workflow and notifications."),
    bullet("Current matching rule-based aur explainable hai; ML future scope hai."),
]
story.append(sub("Last 45 seconds - closing statement"))
story.append(p("In conclusion, SmartWorker ka focus sirf jobs show karna nahi, balke complete recruitment journey ko manage karna hai. Candidate ke liye profile, search, apply aur status tracking; employer ke liye posting, review aur decision making; aur admin ke liye moderation provide ki gayi hai. Laravel MVC aur role-based security ki wajah se system structured, maintainable aur future enhancement ke liye ready hai.", "BodyViva"))
story.append(Spacer(1, 0.75 * cm))
story.append(p("Best of luck for your viva.", "CoverSub"))

doc.build(story, onFirstPage=cover_footer, onLaterPages=page_number)
print(OUT)
