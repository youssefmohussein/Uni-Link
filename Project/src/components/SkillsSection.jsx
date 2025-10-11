import { useState } from "react";

export default function SkillsSection() {
  const [categories, setCategories] = useState([
    "Frontend",
    "Backend",
    "Cloud & DevOps",
  ]);

  const [skills, setSkills] = useState({
    Frontend: ["React", "TypeScript", "Vue.js", "Tailwind CSS"],
    Backend: ["Node.js", "Python", "MongoDB", "PostgreSQL"],
    "Cloud & DevOps": ["AWS", "Docker", "Firebase", "Git"],
  });

  const [showAdd, setShowAdd] = useState(false);
  const [newCategory, setNewCategory] = useState("");
  const [newSkill, setNewSkill] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("");

  const handleAddCategory = () => {
    if (newCategory && !categories.includes(newCategory)) {
      setCategories([...categories, newCategory]);
      setSkills({ ...skills, [newCategory]: [] });
      setNewCategory("");
      setShowAdd(false);
    }
  };

  const handleAddSkill = () => {
    if (selectedCategory && newSkill) {
      setSkills({
        ...skills,
        [selectedCategory]: [...skills[selectedCategory], newSkill],
      });
      setNewSkill("");
      setShowAdd(false);
    }
  };

  return (
    <section className="bg-panel rounded-custom shadow-custom p-6">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-lg font-semibold">💻 Skills</h2>
        <button
          onClick={() => setShowAdd(!showAdd)}
          className="bg-accent text-white px-3 py-1 rounded-custom hover:opacity-80"
        >
          + Add
        </button>
      </div>

      {/* Add input panel */}
      {showAdd && (
        <div className="flex flex-col gap-2 mb-4">
          <select
             className="bg-panel text-main border border-white rounded-custom px-3 py-1 w-full focus:outline-none focus:ring-2 focus:ring-accent"
            value={selectedCategory}
            onChange={(e) => setSelectedCategory(e.target.value)}
          >
            <option value="">Select category</option>
            {categories.map((cat) => (
              <option key={cat} value={cat}>
                {cat}
              </option>
            ))}
          </select>

          <input
            type="text"
            placeholder="Or add new category"
            className="border border-muted rounded-custom px-3 py-1 text-main"
            value={newCategory}
            onChange={(e) => setNewCategory(e.target.value)}
          />

          <input
            type="text"
            placeholder="Add new skill"
            className="border border-muted rounded-custom px-3 py-1 text-main"
            value={newSkill}
            onChange={(e) => setNewSkill(e.target.value)}
          />

          <div className="flex gap-2">
            <button
              onClick={handleAddCategory}
              className="bg-accent text-white px-3 py-1 rounded-custom hover:opacity-80 flex-1"
            >
              Add Category
            </button>
            <button
              onClick={handleAddSkill}
              className="bg-accent text-white px-3 py-1 rounded-custom hover:opacity-80 flex-1"
            >
              Add Skill
            </button>
          </div>
        </div>
      )}

      {/* Display existing categories & skills */}
      <div className="space-y-4">
        {categories.map((category) => (
          <div key={category}>
            <h3 className="font-medium text-main mb-2">{category}</h3>
            <div className="flex flex-wrap gap-2">
              {skills[category].map((skill) => (
                <span
                  key={skill}
                  className="bg-accent/20 text-accent px-2 py-1 rounded-custom text-sm"
                >
                  {skill}
                </span>
              ))}
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}
