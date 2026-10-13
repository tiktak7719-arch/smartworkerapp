from pathlib import Path

from docx import Document
from docx.oxml import OxmlElement
from docx.oxml.ns import qn


DOCX = Path(r"D:\My Web Sites\smartworkerapp\SmartWorker_Thesis_UOS_Format_Shaheen_Shah_Hamad_Khan.docx")


def add_bookmark(paragraph, name: str, identifier: int) -> None:
    element = paragraph._p
    start = OxmlElement("w:bookmarkStart")
    start.set(qn("w:id"), str(identifier))
    start.set(qn("w:name"), name)
    end = OxmlElement("w:bookmarkEnd")
    end.set(qn("w:id"), str(identifier))
    element.insert(1 if element.pPr is not None else 0, start)
    element.append(end)


def main() -> None:
    document = Document(DOCX)
    required = {
        "toc_figures": "List of Figures",
        "toc_references": "References",
    }
    for offset, (bookmark, title) in enumerate(required.items(), 200):
        target = next(
            paragraph
            for paragraph in document.paragraphs
            if paragraph.text.strip() == title and paragraph.style.name.startswith("Heading")
        )
        add_bookmark(target, bookmark, offset)
    document.save(DOCX)


if __name__ == "__main__":
    main()
