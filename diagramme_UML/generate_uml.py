from graphviz import Digraph

# Create an updated UML diagram with all classes included
uml_full = Digraph("UML_Diagram", format="png")
uml_full.attr(rankdir="TB", size="10")

uml_full.attr("node", shape="record", fontname="Helvetica")

# Add the User class
uml_full.node("User", '''User
--------------------
- id: int
- name: string
- email: string
- roles: array
- isVerified: boolean
- certifications: array
--------------------
+ getCertifications()
''', shape="record")

# Add the Theme class
uml_full.node("Theme", '''Theme
--------------------
- id: int
- name: string
- description: string
- lessons: array
--------------------
''', shape="record")

# Add the Cursus class
uml_full.node("Cursus", '''Cursus
--------------------
- id: int
- name: string
- description: string
- themes: array
--------------------
''', shape="record")

# Add the Lesson class
uml_full.node("Lesson", '''Lesson
--------------------
- id: int
- title: string
- description: string
- theme: Theme
- purchases: array
--------------------
''', shape="record")

# Add the Purchase class
uml_full.node("Purchase", '''Purchase
--------------------
- id: int
- user: User
- lesson: Lesson
- status: string
--------------------
''', shape="record")

# Add the Certification class
uml_full.node("Certification", '''Certification
--------------------
- id: int
- name: string
- user: User
- awardedOn: date
--------------------
''', shape="record")

# Define relationships between classes
uml_full.edge("User", "Purchase", "OneToMany")
uml_full.edge("User", "Certification", "OneToMany")
uml_full.edge("Purchase", "Lesson", "ManyToOne")
uml_full.edge("Lesson", "Theme", "ManyToOne")
uml_full.edge("Theme", "Cursus", "ManyToOne")
uml_full.edge("Cursus", "Theme", "OneToMany")

# Render the updated UML diagram
uml_full_filepath = "/Users/emma1/Desktop/CEF/PLATEFORME E-LEARNING « KNOWLEDGE LEARNING »/knowledge-learning/diagramme_UML/knowledge-learning_UML"
uml_full.render(uml_full_filepath, cleanup=True)

uml_full_filepath + ".png"