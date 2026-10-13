from __future__ import annotations

import shutil
import zipfile
from pathlib import Path
from xml.etree import ElementTree as ET

from PIL import Image, ImageDraw, ImageFont
from docx import Document
from docx.enum.section import WD_SECTION_START
from docx.enum.style import WD_STYLE_TYPE
from docx.enum.table import WD_CELL_VERTICAL_ALIGNMENT, WD_TABLE_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_BREAK
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Cm, Inches, Pt, RGBColor


WORK = Path(r"D:\My Web Sites\smartworkerapp\tmp\codex-thesis-work")
REFERENCE = Path(r"C:\Users\Laptop Valley\Downloads\Thesis format.docx")
FINAL = Path(r"D:\My Web Sites\smartworkerapp\SmartWorker_Thesis_Shaheen_Shah_Hamad_Khan.docx")
ASSETS = WORK / "smartworker-figures"

NAVY = "#17365D"
BLUE = "#1F4E79"
LIGHT_BLUE = "#D9EAF7"
MID_BLUE = "#5B9BD5"
LIGHT_GREY = "#F3F6F9"
DARK = "#1E293B"
WHITE = "#FFFFFF"


def qname(tag: str) -> str:
    return qn(tag)


def clear_paragraph(paragraph) -> None:
    p = paragraph._element
    for child in list(p):
        if child.tag != qname("w:pPr"):
            p.remove(child)


def clear_document_body(doc: Document) -> None:
    body = doc._element.body
    for child in list(body):
        if child.tag != qname("w:sectPr"):
            body.remove(child)


def strip_legacy_media(docx_path: Path) -> None:
    """Remove source-template image parts and their image relationships before adding SmartWorker figures."""
    staging = docx_path.with_suffix(".cleaning.docx")
    relationship_type = "http://schemas.openxmlformats.org/officeDocument/2006/relationships/image"
    relationship_ns = "http://schemas.openxmlformats.org/package/2006/relationships"
    with zipfile.ZipFile(docx_path, "r") as source, zipfile.ZipFile(staging, "w", zipfile.ZIP_DEFLATED) as target:
        for item in source.infolist():
            if item.filename.startswith("word/media/"):
                continue
            payload = source.read(item.filename)
            if item.filename.startswith("word/_rels/") and item.filename.endswith(".rels"):
                root = ET.fromstring(payload)
                for relation in list(root):
                    if relation.tag == f"{{{relationship_ns}}}Relationship" and relation.get("Type") == relationship_type:
                        root.remove(relation)
                payload = ET.tostring(root, encoding="utf-8", xml_declaration=True)
            target.writestr(item, payload)
    shutil.move(staging, docx_path)


def prune_unused_template_parts(docx_path: Path) -> None:
    """Remove stale source relationships/parts after the final SmartWorker content is saved."""
    staging = docx_path.with_suffix(".pruned.docx")
    rel_ns = "http://schemas.openxmlformats.org/package/2006/relationships"
    office_rel = "http://schemas.openxmlformats.org/officeDocument/2006/relationships/"
    content_ns = "http://schemas.openxmlformats.org/package/2006/content-types"
    attr_ns = "http://schemas.openxmlformats.org/officeDocument/2006/relationships"
    with zipfile.ZipFile(docx_path, "r") as source:
        document_root = ET.fromstring(source.read("word/document.xml"))
        used_ids = set()
        for element in document_root.iter():
            for attribute_name, attribute_value in element.attrib.items():
                if attribute_name.startswith(f"{{{attr_ns}}}"):
                    used_ids.add(attribute_value)
        relationships_root = ET.fromstring(source.read("word/_rels/document.xml.rels"))
        keep_types = {
            office_rel + "styles",
            office_rel + "numbering",
            office_rel + "settings",
            office_rel + "webSettings",
            office_rel + "theme",
            office_rel + "fontTable",
        }
        retained_targets = set()
        for relation in list(relationships_root):
            relationship_id = relation.get("Id")
            relationship_type = relation.get("Type")
            if relationship_id not in used_ids and relationship_type not in keep_types:
                relationships_root.remove(relation)
                continue
            target = relation.get("Target", "")
            if target and not target.startswith("../") and relation.get("TargetMode") != "External":
                retained_targets.add("word/" + target)
        removed_parts = {
            name
            for name in source.namelist()
            if (
                (name.startswith("word/footer") or name.startswith("word/header"))
                and name.endswith(".xml")
                and name not in retained_targets
            )
            or name in {"word/footnotes.xml", "word/endnotes.xml"}
            or name.startswith("customXml/")
            or name.startswith("word/_rels/footer")
            or name.startswith("word/_rels/header") and name.replace("word/_rels/", "word/").replace(".rels", "") not in retained_targets
        }
        with zipfile.ZipFile(staging, "w", zipfile.ZIP_DEFLATED) as target:
            for item in source.infolist():
                if item.filename in removed_parts:
                    continue
                payload = source.read(item.filename)
                if item.filename == "word/_rels/document.xml.rels":
                    payload = ET.tostring(relationships_root, encoding="utf-8", xml_declaration=True)
                elif item.filename == "[Content_Types].xml":
                    root = ET.fromstring(payload)
                    for override in list(root):
                        if override.tag == f"{{{content_ns}}}Override" and override.get("PartName", "").lstrip("/") in removed_parts:
                            root.remove(override)
                    payload = ET.tostring(root, encoding="utf-8", xml_declaration=True)
                elif item.filename == "_rels/.rels":
                    root = ET.fromstring(payload)
                    for relation in list(root):
                        if relation.tag == f"{{{rel_ns}}}Relationship" and relation.get("Target", "").startswith("customXml/"):
                            root.remove(relation)
                    payload = ET.tostring(root, encoding="utf-8", xml_declaration=True)
                target.writestr(item, payload)
    shutil.move(staging, docx_path)


def set_cell_shading(cell, fill: str) -> None:
    tc_pr = cell._tc.get_or_add_tcPr()
    shd = tc_pr.find(qname("w:shd"))
    if shd is None:
        shd = OxmlElement("w:shd")
        tc_pr.append(shd)
    shd.set(qname("w:fill"), fill.lstrip("#"))
    shd.set(qname("w:val"), "clear")


def set_cell_margins(cell, top=90, start=100, bottom=90, end=100) -> None:
    tc = cell._tc
    tc_pr = tc.get_or_add_tcPr()
    tc_mar = tc_pr.first_child_found_in("w:tcMar")
    if tc_mar is None:
        tc_mar = OxmlElement("w:tcMar")
        tc_pr.append(tc_mar)
    for side, value in (("top", top), ("start", start), ("bottom", bottom), ("end", end)):
        node = tc_mar.find(qname(f"w:{side}"))
        if node is None:
            node = OxmlElement(f"w:{side}")
            tc_mar.append(node)
        node.set(qname("w:w"), str(value))
        node.set(qname("w:type"), "dxa")


def set_repeat_table_header(row) -> None:
    tr_pr = row._tr.get_or_add_trPr()
    marker = OxmlElement("w:tblHeader")
    marker.set(qname("w:val"), "true")
    tr_pr.append(marker)


def set_table_borders(table, color="9FBAD0", size="8") -> None:
    tbl_pr = table._tbl.tblPr
    borders = tbl_pr.first_child_found_in("w:tblBorders")
    if borders is None:
        borders = OxmlElement("w:tblBorders")
        tbl_pr.append(borders)
    for edge in ("top", "left", "bottom", "right", "insideH", "insideV"):
        tag = qname(f"w:{edge}")
        element = borders.find(tag)
        if element is None:
            element = OxmlElement(f"w:{edge}")
            borders.append(element)
        element.set(qname("w:val"), "single")
        element.set(qname("w:sz"), size)
        element.set(qname("w:space"), "0")
        element.set(qname("w:color"), color)


def set_table_widths(table, widths_cm: list[float]) -> None:
    table.autofit = False
    for row in table.rows:
        for cell, width in zip(row.cells, widths_cm):
            cell.width = Cm(width)
            tc_pr = cell._tc.get_or_add_tcPr()
            tc_w = tc_pr.find(qname("w:tcW"))
            if tc_w is None:
                tc_w = OxmlElement("w:tcW")
                tc_pr.append(tc_w)
            tc_w.set(qname("w:w"), str(round(width * 567)))
            tc_w.set(qname("w:type"), "dxa")


def font_run(run, size=11, bold=False, color=DARK, italic=False) -> None:
    run.font.name = "Arial"
    run._element.get_or_add_rPr().get_or_add_rFonts().set(qname("w:ascii"), "Arial")
    run._element.get_or_add_rPr().get_or_add_rFonts().set(qname("w:hAnsi"), "Arial")
    run.font.size = Pt(size)
    run.font.bold = bold
    run.font.italic = italic
    run.font.color.rgb = RGBColor.from_string(color.lstrip("#"))


def set_doc_defaults(doc: Document) -> None:
    normal = doc.styles["Normal"]
    normal.font.name = "Arial"
    normal._element.rPr.rFonts.set(qname("w:ascii"), "Arial")
    normal._element.rPr.rFonts.set(qname("w:hAnsi"), "Arial")
    normal.font.size = Pt(11)
    normal.paragraph_format.space_after = Pt(7)
    normal.paragraph_format.line_spacing = 1.25

    body = doc.styles["Body Text"]
    body.font.name = "Arial"
    body._element.rPr.rFonts.set(qname("w:ascii"), "Arial")
    body._element.rPr.rFonts.set(qname("w:hAnsi"), "Arial")
    body.font.size = Pt(11)
    body.paragraph_format.space_after = Pt(7)
    body.paragraph_format.line_spacing = 1.25

    sizes = {"Heading 1": 16, "Heading 2": 14, "Heading 3": 17, "Heading 4": 13, "Heading 5": 11.5}
    for name, size in sizes.items():
        style = doc.styles[name]
        style.font.name = "Arial"
        style._element.rPr.rFonts.set(qname("w:ascii"), "Arial")
        style._element.rPr.rFonts.set(qname("w:hAnsi"), "Arial")
        style.font.size = Pt(size)
        style.font.bold = True
        style.font.color.rgb = RGBColor.from_string(NAVY.lstrip("#"))
        style.paragraph_format.space_before = Pt(12)
        style.paragraph_format.space_after = Pt(7)
        style.paragraph_format.keep_with_next = True

    if "Caption" not in [style.name for style in doc.styles]:
        caption = doc.styles.add_style("Caption", WD_STYLE_TYPE.PARAGRAPH)
    else:
        caption = doc.styles["Caption"]
    caption.font.name = "Arial"
    caption.font.size = Pt(10)
    caption.font.italic = True
    caption.paragraph_format.space_before = Pt(3)
    caption.paragraph_format.space_after = Pt(10)

    section = doc.sections[0]
    section.page_width = Cm(21.0)
    section.page_height = Cm(29.7)
    section.left_margin = Cm(2.25)
    section.right_margin = Cm(1.75)
    section.top_margin = Cm(2.55)
    section.bottom_margin = Cm(2.60)
    section.header_distance = Cm(1.0)
    section.footer_distance = Cm(1.2)

    for header_footer in (section.header, section.footer):
        header_footer.is_linked_to_previous = False
        for paragraph in header_footer.paragraphs:
            clear_paragraph(paragraph)

    header = section.header.paragraphs[0]
    header.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    run = header.add_run("SMARTWORKER THESIS")
    font_run(run, size=8.5, bold=True, color="6B7280")

    footer = section.footer.paragraphs[0]
    footer.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = footer.add_run("Department of Software Engineering | Academic Session 2022-2026 | Page ")
    font_run(run, size=8.5, color="6B7280")
    add_page_field(footer)

    settings = doc.settings.element
    update = settings.find(qname("w:updateFields"))
    if update is None:
        update = OxmlElement("w:updateFields")
        settings.append(update)
    update.set(qname("w:val"), "true")


def add_page_field(paragraph) -> None:
    run = paragraph.add_run()
    r = run._r
    begin = OxmlElement("w:fldChar")
    begin.set(qname("w:fldCharType"), "begin")
    instruction = OxmlElement("w:instrText")
    instruction.set(qname("xml:space"), "preserve")
    instruction.text = " PAGE "
    separate = OxmlElement("w:fldChar")
    separate.set(qname("w:fldCharType"), "separate")
    text = OxmlElement("w:t")
    text.text = "1"
    end = OxmlElement("w:fldChar")
    end.set(qname("w:fldCharType"), "end")
    r.extend((begin, instruction, separate, text, end))
    font_run(run, size=8.5, color="6B7280")


def add_paragraph(doc: Document, text: str, *, align=None, bold=False, size=11, before=0, after=7, italic=False, color=DARK) -> None:
    paragraph = doc.add_paragraph(style="Body Text")
    paragraph.paragraph_format.space_before = Pt(before)
    paragraph.paragraph_format.space_after = Pt(after)
    paragraph.paragraph_format.line_spacing = 1.25
    paragraph.alignment = align if align is not None else WD_ALIGN_PARAGRAPH.JUSTIFY
    run = paragraph.add_run(text)
    font_run(run, size=size, bold=bold, italic=italic, color=color)
    return paragraph


def add_label_paragraph(doc: Document, label: str, text: str) -> None:
    paragraph = doc.add_paragraph(style="Body Text")
    paragraph.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
    paragraph.paragraph_format.space_after = Pt(5)
    lead = paragraph.add_run(label + " ")
    font_run(lead, size=11, bold=True, color=NAVY)
    copy = paragraph.add_run(text)
    font_run(copy, size=11)


def add_section_heading(doc: Document, title: str, level=4) -> None:
    paragraph = doc.add_paragraph(style=f"Heading {level}")
    paragraph.alignment = WD_ALIGN_PARAGRAPH.LEFT
    run = paragraph.add_run(title)
    font_run(run, size={1: 16, 2: 14, 3: 17, 4: 13, 5: 11.5}.get(level, 11), bold=True, color=NAVY)
    return paragraph


def add_chapter(doc: Document, number: int, title: str) -> None:
    doc.add_page_break()
    paragraph = doc.add_paragraph(style="Heading 3")
    paragraph.alignment = WD_ALIGN_PARAGRAPH.CENTER
    paragraph.paragraph_format.space_before = Pt(20)
    paragraph.paragraph_format.space_after = Pt(18)
    run = paragraph.add_run(f"CHAPTER {number}\n{title.upper()}")
    font_run(run, size=17, bold=True, color=NAVY)


def add_caption(doc: Document, number: int, text: str) -> None:
    paragraph = doc.add_paragraph(style="Caption")
    paragraph.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = paragraph.add_run(f"Figure {number}: {text}")
    font_run(run, size=10, italic=True, color="475569")


def add_table(doc: Document, headers: list[str], rows: list[list[str]], widths: list[float]) -> None:
    table = doc.add_table(rows=1, cols=len(headers))
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    set_table_borders(table)
    set_table_widths(table, widths)
    header_cells = table.rows[0].cells
    for cell, heading in zip(header_cells, headers):
        cell.vertical_alignment = WD_CELL_VERTICAL_ALIGNMENT.CENTER
        set_cell_shading(cell, NAVY)
        set_cell_margins(cell)
        paragraph = cell.paragraphs[0]
        paragraph.alignment = WD_ALIGN_PARAGRAPH.CENTER
        paragraph.paragraph_format.space_after = Pt(0)
        run = paragraph.add_run(heading)
        font_run(run, size=9.5, bold=True, color=WHITE)
    set_repeat_table_header(table.rows[0])
    for row_index, row_values in enumerate(rows):
        cells = table.add_row().cells
        for cell, value in zip(cells, row_values):
            cell.vertical_alignment = WD_CELL_VERTICAL_ALIGNMENT.CENTER
            set_cell_margins(cell)
            if row_index % 2 == 1:
                set_cell_shading(cell, LIGHT_GREY)
            paragraph = cell.paragraphs[0]
            paragraph.alignment = WD_ALIGN_PARAGRAPH.LEFT
            paragraph.paragraph_format.space_after = Pt(0)
            paragraph.paragraph_format.line_spacing = 1.05
            run = paragraph.add_run(value)
            font_run(run, size=9.2)
    doc.add_paragraph().paragraph_format.space_after = Pt(2)
    return table


def draw_box(draw, box, title, detail, fill, outline=NAVY, title_fill=WHITE):
    fill = "#" + fill if not fill.startswith("#") else fill
    outline = "#" + outline if not outline.startswith("#") else outline
    title_fill = "#" + title_fill if not title_fill.startswith("#") else title_fill
    x1, y1, x2, y2 = box
    draw.rounded_rectangle(box, radius=18, fill=fill, outline=outline, width=3)
    draw.rounded_rectangle((x1, y1, x2, y1 + 40), radius=18, fill=outline, outline=outline, width=1)
    draw.rectangle((x1, y1 + 20, x2, y1 + 40), fill=outline)
    title_font = ImageFont.truetype("arialbd.ttf", 21)
    body_font = ImageFont.truetype("arial.ttf", 16)
    draw.text((x1 + 16, y1 + 8), title, font=title_font, fill=title_fill)
    y = y1 + 55
    for line in detail.split("\n"):
        draw.text((x1 + 16, y), line, font=body_font, fill=DARK)
        y += 23


def arrow(draw, start, end, text=None):
    draw.line((*start, *end), fill=MID_BLUE, width=5)
    dx, dy = end[0] - start[0], end[1] - start[1]
    if abs(dx) >= abs(dy):
        sign = 1 if dx > 0 else -1
        points = [(end[0], end[1]), (end[0] - sign * 15, end[1] - 9), (end[0] - sign * 15, end[1] + 9)]
    else:
        sign = 1 if dy > 0 else -1
        points = [(end[0], end[1]), (end[0] - 9, end[1] - sign * 15), (end[0] + 9, end[1] - sign * 15)]
    draw.polygon(points, fill=MID_BLUE)
    if text:
        font = ImageFont.truetype("arial.ttf", 14)
        mid = ((start[0] + end[0]) // 2, (start[1] + end[1]) // 2)
        draw.text((mid[0] + 4, mid[1] - 17), text, font=font, fill="#475569")


def make_figures() -> dict[str, Path]:
    ASSETS.mkdir(parents=True, exist_ok=True)
    files = {}
    font_title = ImageFont.truetype("arialbd.ttf", 28)
    font_small = ImageFont.truetype("arial.ttf", 15)

    # Architecture diagram
    image = Image.new("RGB", (1500, 900), "white")
    draw = ImageDraw.Draw(image)
    draw.text((520, 28), "SmartWorker System Architecture", font=font_title, fill=NAVY)
    draw_box(draw, (70, 180, 390, 400), "Users", "Candidates\nEmployers\nAdministrators", "EEF6FC")
    draw_box(draw, (575, 145, 925, 435), "Laravel Web Application", "Routes and Controllers\nBlade Views\nForm Requests\nRole Middleware", "E8F1FA")
    draw_box(draw, (1110, 120, 1430, 315), "Data Layer", "Eloquent Models\nMigrations\nUsers, jobs, profiles\napplications, messages", "EEF6FC")
    draw_box(draw, (1110, 525, 1430, 735), "Private Storage", "Resumes and CVs\nCompany logos\nProfile avatars", "EEF6FC")
    draw_box(draw, (575, 565, 925, 760), "Business Services", "Explainable matching\nSmart notifications\nAccess-controlled downloads", "E8F1FA")
    arrow(draw, (390, 290), (575, 290), "HTTPS")
    arrow(draw, (925, 255), (1110, 255), "Eloquent")
    arrow(draw, (925, 355), (1110, 610), "validated files")
    arrow(draw, (750, 435), (750, 565), "service calls")
    image_path = ASSETS / "architecture.png"
    image.save(image_path)
    files["architecture"] = image_path

    # Use cases diagram
    image = Image.new("RGB", (1500, 930), "white")
    draw = ImageDraw.Draw(image)
    draw.text((535, 24), "SmartWorker Use Cases", font=font_title, fill=NAVY)
    draw.rounded_rectangle((300, 100, 1210, 860), radius=28, outline=NAVY, width=4, fill="#F9FCFF")
    draw.text((620, 116), "SMARTWORKER", font=ImageFont.truetype("arialbd.ttf", 21), fill=NAVY)
    users = [(90, 240, "Candidate"), (90, 625, "Employer"), (1295, 420, "Administrator")]
    for x, y, label in users:
        draw.ellipse((x, y, x + 48, y + 48), outline=NAVY, width=4)
        draw.line((x + 24, y + 48, x + 24, y + 125), fill=NAVY, width=4)
        draw.line((x - 10, y + 75, x + 58, y + 75), fill=NAVY, width=4)
        draw.line((x + 24, y + 125, x - 5, y + 168), fill=NAVY, width=4)
        draw.line((x + 24, y + 125, x + 53, y + 168), fill=NAVY, width=4)
        draw.text((x - 15, y + 180), label, font=ImageFont.truetype("arialbd.ttf", 18), fill=DARK)
    cases = [
        (465, 205, "Register / Sign in"), (735, 205, "Browse and filter jobs"),
        (465, 390, "Manage profile and resume"), (735, 390, "Apply or save a job"),
        (465, 575, "Post and manage jobs"), (735, 575, "Manage applications"),
        (600, 745, "Chat, ratings and alerts"), (930, 475, "Moderate users and jobs"),
    ]
    for x, y, label in cases:
        draw.ellipse((x, y, x + 245, y + 76), outline=MID_BLUE, width=3, fill="#EAF4FC")
        bbox = draw.textbbox((0, 0), label, font=ImageFont.truetype("arial.ttf", 16))
        draw.text((x + (245 - (bbox[2] - bbox[0])) / 2, y + 28), label, font=ImageFont.truetype("arial.ttf", 16), fill=DARK)
    for start, end in [((138, 315), (465, 243)), ((138, 315), (465, 428)), ((138, 315), (735, 243)), ((138, 315), (735, 428)), ((138, 700), (465, 613)), ((138, 700), (735, 613)), ((138, 700), (720, 783)), ((1295, 495), (1175, 513)), ((1295, 495), (980, 513))]:
        draw.line((*start, *end), fill="#94A3B8", width=3)
    image_path = ASSETS / "use-cases.png"
    image.save(image_path)
    files["use_cases"] = image_path

    # Data model diagram
    image = Image.new("RGB", (1600, 1040), "white")
    draw = ImageDraw.Draw(image)
    draw.text((560, 22), "SmartWorker Data Model", font=font_title, fill=NAVY)
    nodes = {
        "User": (80, 150, 370, 315, "id, name, email, role\nlocation, coordinates\nactive, verified"),
        "UserProfile": (480, 90, 800, 255, "user_id, skills, education\nresume, avatar\ncompany data"),
        "JobListing": (930, 150, 1250, 335, "employer user_id\ncategory_id, title, company\ntags, type, active"),
        "Category": (1330, 90, 1550, 230, "id, name, slug\nactive"),
        "JobApplication": (895, 480, 1240, 645, "job_listing_id, user_id\nCV, cover letter\nstatus, notes"),
        "SavedJob": (470, 490, 760, 630, "user_id, job_listing_id\nunique pair"),
        "Conversation": (100, 510, 390, 670, "candidate_id, employer_id\njob_listing_id"),
        "ChatMessage": (105, 790, 405, 935, "conversation_id, sender_id\nmessage body"),
        "Rating": (1295, 730, 1550, 890, "job_application_id\nreviewer_id, rated_user_id\nscore, comment"),
        "Notification": (500, 785, 810, 935, "user_id, type, body\nURL, data, read_at"),
    }
    for title, (x1, y1, x2, y2, detail) in nodes.items():
        draw_box(draw, (x1, y1, x2, y2), title, detail, "F7FBFF")
    arrows = [
        ((370, 215), (480, 175), "1:1"), ((370, 245), (930, 245), "1:M"), ((1250, 190), (1330, 160), "M:1"),
        ((1090, 335), (1090, 480), "1:M"), ((370, 280), (470, 555), "1:M"), ((760, 555), (930, 555), "M:1"),
        ((370, 580), (470, 580), "candidate/employer"), ((245, 670), (245, 790), "1:M"),
        ((1240, 600), (1295, 800), "1:M"), ((370, 300), (500, 840), "1:M"),
    ]
    for start, end, label in arrows:
        arrow(draw, start, end, label)
    image_path = ASSETS / "data-model.png"
    image.save(image_path)
    files["data_model"] = image_path

    # Matching workflow
    image = Image.new("RGB", (1500, 720), "white")
    draw = ImageDraw.Draw(image)
    draw.text((510, 28), "Explainable Job Matching Workflow", font=font_title, fill=NAVY)
    boxes = [
        (70, 260, 340, 455, "Candidate Profile", "Skills\nLocation / coordinates\nRatings"),
        (455, 260, 745, 455, "Matching Service", "Skill comparison\nLocation score\nReputation score"),
        (860, 260, 1150, 455, "Score", "Transparent 0-100\nThreshold filtering\nRanked results"),
        (1260, 260, 1470, 455, "Output", "Recommendations\nJob alerts\nEmployer matches"),
    ]
    for x1, y1, x2, y2, title, detail in boxes:
        draw_box(draw, (x1, y1, x2, y2), title, detail, "EEF6FC")
    arrow(draw, (340, 355), (455, 355))
    arrow(draw, (745, 355), (860, 355))
    arrow(draw, (1150, 355), (1260, 355))
    draw.text((450, 520), "Current implementation weights skills, location, and reputation for an explainable recommendation.", font=font_small, fill="#475569")
    image_path = ASSETS / "matching-workflow.png"
    image.save(image_path)
    files["matching"] = image_path
    return files


def cover_page(doc: Document) -> None:
    for _ in range(5):
        doc.add_paragraph()
    title = doc.add_paragraph()
    title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    title.paragraph_format.space_after = Pt(12)
    run = title.add_run("SMARTWORKER")
    font_run(run, size=25, bold=True, color=NAVY)
    subtitle = doc.add_paragraph()
    subtitle.alignment = WD_ALIGN_PARAGRAPH.CENTER
    subtitle.paragraph_format.space_after = Pt(24)
    run = subtitle.add_run("Intelligent Job Portal and Recruitment Management System")
    font_run(run, size=15, bold=True, color=DARK)
    statement = doc.add_paragraph()
    statement.alignment = WD_ALIGN_PARAGRAPH.CENTER
    statement.paragraph_format.space_after = Pt(24)
    run = statement.add_run("A Final Year Project Thesis")
    font_run(run, size=12, italic=True, color="475569")
    add_paragraph(doc, "Submitted by:", align=WD_ALIGN_PARAGRAPH.CENTER, bold=True, size=12, after=8, color=NAVY)
    add_paragraph(doc, "Shaheen Shah", align=WD_ALIGN_PARAGRAPH.CENTER, size=12, after=3)
    add_paragraph(doc, "Hamad Khan", align=WD_ALIGN_PARAGRAPH.CENTER, size=12, after=28)
    add_paragraph(doc, "Department of Software Engineering", align=WD_ALIGN_PARAGRAPH.CENTER, bold=True, size=12, after=5, color=NAVY)
    add_paragraph(doc, "Academic Session 2022-2026", align=WD_ALIGN_PARAGRAPH.CENTER, size=11, after=0)


def front_matter(doc: Document) -> None:
    doc.add_page_break()
    add_section_heading(doc, "Declaration", 2)
    add_paragraph(doc, "We, Shaheen Shah and Hamad Khan, declare that this thesis titled SmartWorker: Intelligent Job Portal and Recruitment Management System is our own final year project work. The project report has been prepared from the implemented system and has not been submitted, in whole or in part, for any other degree or qualification.")
    add_paragraph(doc, "Student: Shaheen Shah  __________________________", align=WD_ALIGN_PARAGRAPH.LEFT, after=14)
    add_paragraph(doc, "Student: Hamad Khan   __________________________", align=WD_ALIGN_PARAGRAPH.LEFT, after=14)
    add_paragraph(doc, "Date: __________________________", align=WD_ALIGN_PARAGRAPH.LEFT, after=0)

    doc.add_page_break()
    add_section_heading(doc, "Acknowledgements", 2)
    add_paragraph(doc, "We begin by thanking Almighty Allah for giving us the strength and patience to complete this final year project. We are grateful to our parents, teachers, classmates, and everyone who supported us during the analysis, development, testing, and documentation of SmartWorker. Their encouragement and feedback helped us complete this work.")
    add_paragraph(doc, "Shaheen Shah and Hamad Khan", align=WD_ALIGN_PARAGRAPH.RIGHT, italic=True, after=0)

    doc.add_page_break()
    add_section_heading(doc, "Abstract", 1)
    add_paragraph(doc, "SmartWorker is a web-based job portal and recruitment management system that connects job candidates, employers, and administrators through a single role-aware platform. The project addresses common recruitment challenges such as fragmented job discovery, unstructured candidate profiles, manual application tracking, and the difficulty of identifying relevant opportunities.")
    add_paragraph(doc, "The application is implemented with Laravel, PHP, Blade templates, Vite, and Tailwind CSS. Candidates can create profiles, upload resumes, search and save jobs, submit applications, receive notifications, use in-platform chat, and view job recommendations. Employers can manage company information, post job listings, review applications, view suggested candidates, and communicate with candidates. Administrators moderate users, jobs, contact messages, and applications. The system also implements an explainable matching service that considers skills, location, and rating information, alongside access controls, validation, private document handling, password hashing, CSRF protection, and security headers.")
    add_paragraph(doc, "SmartWorker demonstrates how a role-based web system can make recruitment workflows more organised, transparent, and accessible. The project provides a foundation for future additions such as richer analytics, asynchronous processing, mobile clients, and more advanced recommendation models.")
    add_paragraph(doc, "Keywords: job portal, recruitment, Laravel, role-based access control, job matching, notifications, web application.", bold=True, after=0)

    doc.add_page_break()
    add_section_heading(doc, "Table of Contents", 1)
    contents = [
        "Declaration", "Acknowledgements", "Abstract", "Chapter 1 - Introduction", "Chapter 2 - Software Development Methodology", "Chapter 3 - Software Requirements Specification", "Chapter 4 - System Design", "Chapter 5 - Implementation", "Chapter 6 - Testing", "Chapter 7 - Conclusion and Future Work", "References",
    ]
    for item in contents:
        add_paragraph(doc, item, align=WD_ALIGN_PARAGRAPH.LEFT, size=11, after=4)
    add_paragraph(doc, "Note: page numbers in the footer update automatically when this document is opened in Microsoft Word.", italic=True, size=9.5, after=0, color="64748B")


def chapter_one(doc: Document) -> None:
    add_chapter(doc, 1, "Introduction")
    add_section_heading(doc, "1.1 Introduction")
    add_paragraph(doc, "Digital recruitment depends on timely access to opportunities, reliable candidate information, and clear communication between job seekers and employers. When these activities are handled through separate spreadsheets, messaging applications, and unstructured advertisements, both applicants and recruiters spend unnecessary time locating information and following up on progress. SmartWorker was developed as a central web platform for job discovery and recruitment management.")
    add_section_heading(doc, "1.2 Problem Statement")
    add_paragraph(doc, "Candidates often need a single place to create a professional profile, discover jobs, apply with supporting documents, and monitor progress. Employers need a controlled workflow for posting jobs, reviewing applications, finding suitable candidates, and communicating with applicants. A system is therefore required that provides these functions while enforcing role boundaries, validating input, and protecting personal documents.")
    add_section_heading(doc, "1.3 Scope of the Project")
    add_paragraph(doc, "The scope of SmartWorker is a browser-based recruitment platform for three principal roles: candidate, employer, and administrator. It covers account registration, profile management, job and category browsing, job posting, applications, saved jobs, private resume/CV download rules, notifications, chat, ratings, public directories, blog content, contact messages, and administrative moderation. It does not claim to replace an organisation's full human-resource information system or payroll process.")
    add_section_heading(doc, "1.4 Objectives")
    objectives = [
        ("Centralise recruitment.", "Provide a single platform where candidates search and apply for jobs while employers create and manage listings."),
        ("Support role-specific workflows.", "Give candidates, employers, and administrators access only to the actions required for their role."),
        ("Improve matching.", "Rank relevant jobs and candidates using an explainable score based on skills, location, and reputation data."),
        ("Organise applications.", "Allow applicants to attach a CV and cover letter, and allow employers to manage application status and notes."),
        ("Protect user information.", "Use authentication, validation, private document storage, permission checks, and security response headers."),
        ("Enable communication.", "Provide notifications and one-to-one conversation threads linked to recruitment activity."),
    ]
    for label, text in objectives:
        add_label_paragraph(doc, label, text)
    add_section_heading(doc, "1.5 Key Features")
    features = [
        ["Candidate services", "Profile, skills, resume upload, job browsing, saved jobs, applications, recommendations, notifications, chat."],
        ["Employer services", "Company profile, job creation, job management, application review, candidate browsing, recommended candidates, chat."],
        ["Administration", "Dashboard, user activation and verification, job moderation, contact-message handling, application oversight."],
        ["Smart functions", "Skill and location matching, rating-informed scores, job-match alerts, category browsing, multilingual route support."],
        ["Protection", "Role middleware, password hashing, CSRF protection, input validation, private document downloads, security headers."],
    ]
    add_table(doc, ["Area", "Implemented capability"], features, [4.0, 12.7])
    add_section_heading(doc, "1.6 Project Benefits")
    add_paragraph(doc, "SmartWorker reduces the effort required to move between job advertisements, application records, and separate communication channels. Candidates receive a structured way to represent their skills and follow applications. Employers receive controlled job-management and applicant-review tools. Administrators retain oversight of active accounts, listings, and public contact messages. The explainable recommendation score also makes matching factors understandable instead of presenting an unexplained result.")


def chapter_two(doc: Document) -> None:
    add_chapter(doc, 2, "Software Development Methodology")
    add_section_heading(doc, "2.1 Development Approach")
    add_paragraph(doc, "SmartWorker follows an iterative Agile-oriented approach. Work is divided into short, reviewable increments: requirement identification, interface and data-model design, core implementation, validation and testing, and refinement. This approach suits a web application because its role workflows, validation rules, and interface requirements can be reviewed and improved while the project is developed.")
    add_section_heading(doc, "2.2 Development Phases")
    phases = [
        ["Requirements and planning", "Identify candidate, employer, and administrator workflows; define the core data entities and access rules."],
        ["Interface and data design", "Prepare job portal pages, dashboards, relationships, migrations, and validation strategy."],
        ["Core implementation", "Implement authentication, profiles, job listings, applications, role middleware, views, and administration."],
        ["Smart features", "Add matching, notifications, chat, ratings, protected documents, directories, and content modules."],
        ["Testing and refinement", "Run automated feature tests, inspect major workflows, and correct implementation issues."],
        ["Documentation", "Prepare diagrams, system description, testing evidence, conclusion, and this thesis."],
    ]
    add_table(doc, ["Phase", "Main activities"], phases, [4.2, 12.5])
    add_section_heading(doc, "2.3 Tools and Technologies")
    technologies = [
        ["Backend", "PHP 8.3 and Laravel 13", "Routing, controllers, Eloquent models, validation, middleware, migrations."],
        ["Presentation", "Blade templates, Vite, Tailwind CSS", "Responsive pages, layout components, asset building."],
        ["Data", "Relational database through Laravel migrations", "Users, profiles, jobs, applications, messages, ratings, notifications."],
        ["Development", "Visual Studio Code, Composer, npm", "Source editing, dependency management, local development."],
        ["Testing", "PHPUnit / Laravel feature tests", "Authentication, role access, job posting, applications, legacy routes."],
        ["Deployment", "Render blueprint configuration", "Web-service and database deployment configuration for a demo environment."],
    ]
    add_table(doc, ["Layer", "Technology", "Purpose"], technologies, [3.1, 4.2, 9.4])
    add_section_heading(doc, "2.4 Project Deliverables")
    deliverables = [
        ["Responsive web portal", "Public job discovery and role-specific dashboards."],
        ["Recruitment workflows", "Job posting, applications, saved jobs, status handling, applicant management."],
        ["Smart services", "Explainable matching and notifications for relevant jobs and candidates."],
        ["Administrative control", "User/job moderation, verification, contacts, and application overview."],
        ["Technical documentation", "Requirements, design, architecture, data model, testing, and user-facing features."],
    ]
    add_table(doc, ["Deliverable", "Expected result"], deliverables, [5.0, 11.7])
    add_section_heading(doc, "2.5 Risk Plan")
    risks = [
        ["Role-access error", "Medium", "Role middleware and feature tests restrict routes to active permitted accounts."],
        ["Invalid or unsafe input", "Medium", "Laravel Form Requests validate registration, jobs, profiles, and contact data."],
        ["Sensitive document exposure", "High", "Private storage plus user and role checks before resume or CV download."],
        ["Matching quality", "Medium", "Transparent scoring makes weights inspectable and allows later adjustment."],
        ["Deployment configuration", "Medium", "Environment configuration and Render blueprint provide a repeatable demo setup."],
    ]
    add_table(doc, ["Risk", "Level", "Mitigation"], risks, [3.6, 2.0, 11.1])


def chapter_three(doc: Document, figures: dict[str, Path]) -> None:
    add_chapter(doc, 3, "Software Requirements Specification")
    add_section_heading(doc, "3.1 System Overview")
    add_paragraph(doc, "The SmartWorker system must support public job discovery and secure recruitment workflows for authenticated users. Its design distinguishes roles clearly: candidates find and apply for jobs, employers manage hiring activity, and administrators moderate platform resources. The functional requirements are derived from the implemented routes, controllers, models, Form Requests, and middleware.")
    add_section_heading(doc, "3.2 Users and Roles")
    roles = [
        ["Visitor", "Browse public jobs, categories, candidates/employers directories, blog, information, privacy, and terms pages."],
        ["Candidate", "Maintain profile/resume, browse/save/apply for jobs, follow applications, receive alerts, chat, and rate after an application."],
        ["Employer", "Maintain company profile, post/edit/delete jobs, review applications, browse resumes, access candidate matches, chat."],
        ["Administrator", "Moderate users and jobs, verify accounts, view contacts and applications, and keep platform content controlled."],
    ]
    add_table(doc, ["Actor", "Responsibilities"], roles, [3.2, 13.5])
    add_section_heading(doc, "3.3 Functional Requirements")
    requirements = [
        ["FR-01", "Account registration and authentication", "The system shall register candidate and employer accounts, authenticate users, support logout and password reset."],
        ["FR-02", "Profile management", "The system shall allow users to update personal/company details, skills, location, documents, and media."],
        ["FR-03", "Job discovery", "The system shall provide searchable jobs, categories, individual job details, and saved-job functionality."],
        ["FR-04", "Job management", "The system shall allow employers and authorised administrators to create, edit, activate, and remove job listings."],
        ["FR-05", "Applications", "The system shall let candidates submit one application per job, attach a CV, and view their application history."],
        ["FR-06", "Hiring workflow", "The system shall let employers view relevant applications and update each application status."],
        ["FR-07", "Recommendations", "The system shall produce transparent job/candidate suggestions using skill, location, and rating signals."],
        ["FR-08", "Communication", "The system shall create controlled conversations, messages, notifications, and post-application ratings."],
        ["FR-09", "Administration", "The system shall let administrators manage users, verification, job visibility, contacts, and applications."],
    ]
    add_table(doc, ["ID", "Function", "Requirement"], requirements, [1.5, 4.0, 11.2])
    add_section_heading(doc, "3.4 Non-Functional Requirements")
    nonfunctional = [
        ["Security", "Password hashing, CSRF protection, private document access, request validation, security headers, and role checks."],
        ["Usability", "Clear public navigation and separate dashboard workflows for each role."],
        ["Reliability", "Database constraints support unique applications and saved jobs; validation rejects invalid input."],
        ["Maintainability", "Laravel MVC organisation separates routes, controllers, models, Form Requests, middleware, and services."],
        ["Scalability", "The architecture supports later background processing, additional notification channels, and matching-service replacement."],
    ]
    add_table(doc, ["Quality attribute", "Requirement"], nonfunctional, [4.2, 12.5])
    add_section_heading(doc, "3.5 Use-Case Model")
    add_paragraph(doc, "The use-case diagram summarises how each role interacts with the major features. Candidate and employer operations are isolated through role middleware, while administration remains separate from employer controls.")
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.add_run().add_picture(str(figures["use_cases"]), width=Inches(6.4))
    add_caption(doc, 1, "SmartWorker use-case diagram")


def chapter_four(doc: Document, figures: dict[str, Path]) -> None:
    add_chapter(doc, 4, "System Design")
    add_section_heading(doc, "4.1 Architectural Design")
    add_paragraph(doc, "SmartWorker uses a Laravel MVC architecture. Browser requests are mapped through named routes to controllers. Controllers coordinate Form Request validation, Eloquent data access, service logic, and Blade views. The matching and notification services hold cross-cutting business operations. Private documents are stored separately and are returned only after authentication and authorisation checks.")
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.add_run().add_picture(str(figures["architecture"]), width=Inches(6.4))
    add_caption(doc, 2, "High-level SmartWorker system architecture")
    add_section_heading(doc, "4.2 Data Design")
    add_paragraph(doc, "The relational data model centres on the User entity. A user can have a profile and may act as a candidate, employer, or administrator. Employers own job listings; candidates create applications and saved-job records. Conversations connect a candidate and employer and can optionally relate to a job. Ratings are attached to a completed application context, and notifications belong to individual users.")
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.add_run().add_picture(str(figures["data_model"]), width=Inches(6.35))
    add_caption(doc, 3, "Core SmartWorker data model")
    add_section_heading(doc, "4.3 Important Relationships")
    relationships = [
        ["User - UserProfile", "One to one", "Each user may own a profile that stores candidate or company details."],
        ["User - JobListing", "One to many", "An employer can create multiple job listings."],
        ["JobListing - JobApplication", "One to many", "A listing can receive multiple applications; candidate/listing pairs are unique."],
        ["User - SavedJob - JobListing", "Many to many", "Candidates save individual jobs through a unique join record."],
        ["Conversation - ChatMessage", "One to many", "A conversation contains messages authored by a permitted participant."],
        ["JobApplication - Rating", "One to many", "Ratings record reviewer and rated user information in an application context."],
        ["User - SmartNotification", "One to many", "Individual users receive application and recommendation alerts."],
    ]
    add_table(doc, ["Entities", "Cardinality", "Meaning"], relationships, [4.3, 3.0, 9.6])
    add_section_heading(doc, "4.4 Access-Control Design")
    add_paragraph(doc, "The role middleware first checks that the request has an authenticated, active user. It then permits the route only when the user's role is one of the listed roles. Employer routes accept employer or administrator roles; candidate routes accept candidate users; and administration is protected by an administrator-only route group. Private resume and CV download controllers also check ownership or permitted employer/admin access before serving a document.")
    add_section_heading(doc, "4.5 Matching Design")
    add_paragraph(doc, "The matching service is deliberately explainable. For job recommendations it compares profile skills with a listing corpus, evaluates location compatibility, and adds a limited score from received ratings. Candidate recommendations apply the same principle from the employer perspective. The design helps future reviewers understand the reason for a recommendation and permits the scoring method to be replaced by a machine-learning service later.")
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.add_run().add_picture(str(figures["matching"]), width=Inches(6.4))
    add_caption(doc, 4, "Explainable matching workflow")


def chapter_five(doc: Document) -> None:
    add_chapter(doc, 5, "Implementation")
    add_section_heading(doc, "5.1 Implementation Overview")
    add_paragraph(doc, "SmartWorker is implemented as a Laravel application. Its web routes define the public area, authentication, employer-only actions, candidate-only actions, shared account controls, and administrator functions. Controller classes then coordinate the views and model operations, while Form Request classes centralise validation rules for important user input.")
    add_section_heading(doc, "5.2 Authentication and Role Workflows")
    add_paragraph(doc, "Registration accepts candidate and employer account types. Authentication includes login, logout, forgotten-password, and password-reset workflows. The EnsureUserHasRole middleware blocks inactive users and denies requests whose role is not allowed by the route. This keeps candidates from entering employer functions and separates administrator moderation from ordinary employer control.")
    add_section_heading(doc, "5.3 Job and Application Management")
    add_paragraph(doc, "Employers can create, modify, activate, or delete job listings. A listing stores a title, company, category, location, employment type, compensation information, requirements, benefits, tags, and application settings. Candidates can browse active jobs, view details, save jobs, apply once per listing, attach a CV, and track application status. Employers can review applications for their own listings and move them through pending, reviewing, shortlisted, rejected, or hired states.")
    add_section_heading(doc, "5.4 Profiles, Documents, and Directories")
    add_paragraph(doc, "The profile module stores candidate information such as skills, education, experience, social links, resume, and avatar. For employers it stores company information, industry, size, description, website, and logo. Document-download controllers enforce access rules so that documents are not exposed publicly. The directories provide browsable candidate and employer records according to platform permissions.")
    add_section_heading(doc, "5.5 Recommendations, Alerts, and Communication")
    add_paragraph(doc, "The MatchingService produces ranked jobs for candidates and ranked candidates for employers. It combines skill overlap, geographic proximity or remote compatibility, and ratings. The SmartNotificationService creates a job-match alert when a suitable job is published and a candidate has alerts enabled. The chat module creates a conversation between a candidate and employer and stores text messages within that conversation. Ratings can be recorded in connection with an application.")
    add_section_heading(doc, "5.6 Administrative Functions")
    add_paragraph(doc, "The administrator dashboard provides system-level oversight. Administrators can inspect and toggle job visibility, deactivate or verify users, delete unsuitable content, handle contact messages, and view application records. These operations are isolated in an administrator-prefixed route group, maintaining a separation of duties from employer workflows.")
    add_section_heading(doc, "5.7 Security Measures")
    security = [
        ["Authentication", "Laravel authentication workflows and hashed passwords."],
        ["Authorisation", "Role middleware and ownership checks on sensitive actions."],
        ["Validation", "Form Requests validate registration, profiles, contacts, and job listing input."],
        ["Files", "File type/size rules and protected download paths for resumes and CVs."],
        ["HTTP protections", "CSRF protection plus content-type, frame, referrer, permissions, and opener headers."],
    ]
    add_table(doc, ["Control", "Implementation"], security, [4.0, 12.7])


def chapter_six(doc: Document) -> None:
    add_chapter(doc, 6, "Testing")
    add_section_heading(doc, "6.1 Testing Strategy")
    add_paragraph(doc, "Testing focused on the workflows that protect access, process job postings, and manage applications. The repository includes Laravel feature tests for authentication, role access, job posting, the application workflow, post-job pages, and legacy route compatibility. Automated testing complements controller-level validation and manual inspection of the major role workflows.")
    add_section_heading(doc, "6.2 Automated Test Execution")
    add_paragraph(doc, "The project test command was executed during preparation of this thesis. The run completed without a test failure: 13 tests were registered, 5 passed, 8 were skipped, and 10 assertions were reported. The test runner also emitted a non-blocking warning because it could not update the PHPUnit result-cache file in the workspace; this did not cause a test failure.")
    add_section_heading(doc, "6.3 Representative Test Cases")
    tests = [
        ["Authentication", "Register and login with valid details", "User is authenticated and directed to the appropriate role workflow."],
        ["Role access", "Candidate requests employer route; employer requests candidate route", "Middleware rejects access when the role does not match."],
        ["Job posting", "Employer submits a valid job listing", "Validated listing is stored and becomes manageable by its owner."],
        ["Application workflow", "Candidate submits an application for an active job", "Application is stored once per candidate/job pair and is visible to the employer."],
        ["Documents", "Unauthorised request for protected CV/resume", "Controller checks access before serving the file."],
        ["Notifications", "A qualifying job is published for an alert-enabled candidate", "The matching service can create a targeted job-match notification."],
    ]
    add_table(doc, ["Area", "Test activity", "Expected result"], tests, [3.0, 5.5, 8.2])
    add_section_heading(doc, "6.4 Validation Results")
    add_paragraph(doc, "The implemented validation rules reject incomplete registration data, duplicate email addresses, weak or unmatched passwords, invalid job types, incorrect location coordinates, invalid URLs, invalid file types, and invalid salary ranges. Database uniqueness constraints reinforce key rules such as one saved-job record per candidate/listing and one application per candidate/listing.")
    add_section_heading(doc, "6.5 Limitations of Current Testing")
    add_paragraph(doc, "The automated test suite gives useful feature-level confidence, but it is not a complete load, accessibility, browser compatibility, or penetration-test programme. The skipped tests should be reviewed and enabled where their prerequisites are available. A production release should add broader regression coverage, browser tests, performance checks, audit logging, and independent security assessment.")


def chapter_seven(doc: Document) -> None:
    add_chapter(doc, 7, "Conclusion and Future Work")
    add_section_heading(doc, "7.1 Conclusion")
    add_paragraph(doc, "SmartWorker delivers an organised web platform for job discovery and recruitment management. It unifies candidate profiles, resumes, job listings, applications, employer workflows, communication, notifications, ratings, and administration within a single Laravel application. Its role-based design, validation, private-document access rules, and security headers support safer handling of recruitment information.")
    add_paragraph(doc, "The explainable matching component distinguishes the project by showing how skills, location, and reputation can contribute to recommendations without hiding the scoring logic. The implemented feature tests provide a baseline for authentication, job posting, applications, route access, and compatibility behaviour. Overall, the project meets its objective of providing a practical foundation for candidates, employers, and administrators to manage recruitment interactions in one place.")
    add_section_heading(doc, "7.2 Future Work")
    future = [
        ("Expanded recommendation model.", "Introduce configurable weights, richer semantic skill matching, and carefully evaluated machine-learning recommendations."),
        ("Mobile clients.", "Provide Android and iOS applications that consume a secured API."),
        ("Advanced analytics.", "Add dashboard metrics for vacancies, application conversion, response times, and hiring funnel performance."),
        ("Asynchronous processing.", "Move large notifications, document analysis, and high-volume tasks to queues."),
        ("Accessibility and localisation.", "Extend language support and perform formal accessibility testing across key flows."),
        ("Security hardening.", "Add audit logs, stronger monitoring, rate limiting review, security testing, and retention controls."),
    ]
    for label, text in future:
        add_label_paragraph(doc, label, text)
    add_section_heading(doc, "References", 1)
    references = [
        "Laravel. Laravel Documentation. https://laravel.com/docs",
        "PHP Documentation. https://www.php.net/docs.php",
        "Tailwind CSS. Documentation. https://tailwindcss.com/docs",
        "Vite. Guide. https://vite.dev/guide/",
        "PHPUnit. Documentation. https://docs.phpunit.de/",
        "OWASP Foundation. Cheat Sheet Series. https://cheatsheetseries.owasp.org/",
        "SmartWorker project source code: routes, controllers, models, migrations, services, Form Requests, and feature tests (local project repository).",
    ]
    for reference in references:
        add_paragraph(doc, reference, align=WD_ALIGN_PARAGRAPH.LEFT, size=10.5, after=5)


def cleanup_visible_text(docx_path: Path) -> None:
    # The document is built from a copy to retain the reference's styles, but old body content is removed before authoring.
    # Saving through python-docx can leave unused source media parts; they are not referenced by the final document body.
    document = Document(docx_path)
    text = "\n".join(paragraph.text for paragraph in document.paragraphs)
    forbidden = ("VIRTUAL STUDY GROUP", "Sharjeel Muhammad", "Luqman Khan")
    present = [word for word in forbidden if word.lower() in text.lower()]
    if present:
        raise RuntimeError(f"Old reference text remains in final document: {present}")


def main() -> None:
    if not REFERENCE.exists():
        raise FileNotFoundError(REFERENCE)
    figures = make_figures()
    shutil.copy2(REFERENCE, FINAL)
    doc = Document(FINAL)
    clear_document_body(doc)
    # Save the cleared working copy before compacting away the reference's project-specific screenshots.
    doc.save(FINAL)
    strip_legacy_media(FINAL)
    doc = Document(FINAL)
    set_doc_defaults(doc)
    doc.core_properties.title = "SmartWorker: Intelligent Job Portal and Recruitment Management System"
    doc.core_properties.author = "Shaheen Shah and Hamad Khan"
    doc.core_properties.subject = "Final Year Project Thesis"
    doc.core_properties.keywords = "SmartWorker, job portal, Laravel, recruitment"

    cover_page(doc)
    front_matter(doc)
    chapter_one(doc)
    chapter_two(doc)
    chapter_three(doc, figures)
    chapter_four(doc, figures)
    chapter_five(doc)
    chapter_six(doc)
    chapter_seven(doc)

    doc.save(FINAL)
    prune_unused_template_parts(FINAL)
    cleanup_visible_text(FINAL)
    print(FINAL)


if __name__ == "__main__":
    main()
