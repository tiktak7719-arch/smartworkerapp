from __future__ import annotations

import re
import shutil
from pathlib import Path

from docx import Document
from docx.oxml import OxmlElement
from docx.oxml.ns import qn


DOCX = Path(r"D:\My Web Sites\smartworkerapp\SmartWorker_Thesis_UOS_Format_Shaheen_Shah_Hamad_Khan.docx")
STAGING = DOCX.with_suffix(".toc.docx")


def normalize(value: str) -> str:
    return re.sub(r"\s+", " ", value).strip().casefold()


def add_bookmark(paragraph, name: str, identifier: int) -> None:
    element = paragraph._p
    start = OxmlElement("w:bookmarkStart")
    start.set(qn("w:id"), str(identifier))
    start.set(qn("w:name"), name)
    end = OxmlElement("w:bookmarkEnd")
    end.set(qn("w:id"), str(identifier))
    insertion_index = 1 if element.pPr is not None else 0
    element.insert(insertion_index, start)
    element.append(end)


def make_hyperlink_run(text: str):
    hyperlink = OxmlElement("w:hyperlink")
    hyperlink.set(qn("w:anchor"), "")
    hyperlink.set(qn("w:history"), "1")
    run = OxmlElement("w:r")
    properties = OxmlElement("w:rPr")
    fonts = OxmlElement("w:rFonts")
    for attribute in ("w:ascii", "w:hAnsi", "w:eastAsia", "w:cs"):
        fonts.set(qn(attribute), "Times New Roman")
    properties.append(fonts)
    size = OxmlElement("w:sz")
    size.set(qn("w:val"), "24")
    properties.append(size)
    color = OxmlElement("w:color")
    color.set(qn("w:val"), "000000")
    properties.append(color)
    underline = OxmlElement("w:u")
    underline.set(qn("w:val"), "none")
    properties.append(underline)
    run.append(properties)
    text_element = OxmlElement("w:t")
    text_element.text = text
    run.append(text_element)
    hyperlink.append(run)
    return hyperlink


def replace_with_internal_link(paragraph, text: str, bookmark: str) -> None:
    element = paragraph._p
    for child in list(element):
        if child.tag != qn("w:pPr"):
            element.remove(child)
    hyperlink = make_hyperlink_run(text)
    hyperlink.set(qn("w:anchor"), bookmark)
    element.append(hyperlink)


def main() -> None:
    document = Document(DOCX)
    paragraphs = document.paragraphs
    targets = {
        "Declaration": "toc_declaration",
        "Forwarding Sheet": "toc_forwarding",
        "Dedication": "toc_dedication",
        "Final Approval": "toc_approval",
        "Acknowledgements": "toc_acknowledgements",
        "Abstract": "toc_abstract",
        "List of Figures": "toc_figures",
        "Chapter 1: Introduction": "toc_chapter_1",
        "Chapter 2: Software Development Methodology": "toc_chapter_2",
        "Chapter 3: Software Requirement Specification": "toc_chapter_3",
        "Chapter 4: System Design": "toc_chapter_4",
        "Chapter 5: Implementation": "toc_chapter_5",
        "Chapter 6: Testing": "toc_chapter_6",
        "Chapter 7: Conclusion and Future Work": "toc_chapter_7",
        "References": "toc_references",
        "Appendix A: SmartWorker Screenshots": "toc_appendix_a",
    }
    target_matchers = {
        "Declaration": "declaration",
        "Forwarding Sheet": "forwarding sheet",
        "Dedication": "dedication",
        "Final Approval": "final approval",
        "Acknowledgements": "acknowledgements",
        "Abstract": "abstract",
        "List of Figures": "list of figures",
        "Chapter 1: Introduction": "chapter 1 introduction",
        "Chapter 2: Software Development Methodology": "chapter 2 software development methodology",
        "Chapter 3: Software Requirement Specification": "chapter 3 software requirement specification",
        "Chapter 4: System Design": "chapter 4 system design",
        "Chapter 5: Implementation": "chapter 5 implementation",
        "Chapter 6: Testing": "chapter 6 testing",
        "Chapter 7: Conclusion and Future Work": "chapter 7 conclusion and future work",
        "References": "references",
        "Appendix A: SmartWorker Screenshots": "appendix a",
    }
    target_paragraphs = {}
    for entry, matcher in target_matchers.items():
        for paragraph in paragraphs:
            if normalize(paragraph.text) == matcher:
                target_paragraphs[entry] = paragraph
                break
    missing = sorted(set(targets) - set(target_paragraphs))
    if missing:
        raise RuntimeError(f"Could not locate TOC targets: {missing}")

    bookmark_id = 100
    for entry, bookmark in targets.items():
        add_bookmark(target_paragraphs[entry], bookmark, bookmark_id)
        bookmark_id += 1

    toc_heading_index = next(index for index, paragraph in enumerate(paragraphs) if normalize(paragraph.text) == "table of contents")
    toc_entries = paragraphs[toc_heading_index + 1 : toc_heading_index + 1 + len(targets)]
    expected = list(targets)
    found = [paragraph.text.strip() for paragraph in toc_entries]
    if found != expected:
        raise RuntimeError(f"TOC entries do not match expected sequence: {found}")
    for entry, paragraph in zip(expected, toc_entries):
        replace_with_internal_link(paragraph, entry, targets[entry])

    document.save(STAGING)
    shutil.move(STAGING, DOCX)
    print(DOCX)


if __name__ == "__main__":
    main()
