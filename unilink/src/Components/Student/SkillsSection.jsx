import { useState, useEffect } from "react";
import * as profileHandler from "../../../api/profileHandler";

export default function SkillsSection({ userId }) {
  const [skills, setSkills] = useState({});
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [newCategory, setNewCategory] = useState("");
  const [newSkill, setNewSkill] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("");

  useEffect(() => {
    if (userId) {
      fetchUserSkills();
    }
  }, [userId]);

  const fetchUserSkills = async () => {
    try {
      setLoading(true);
      const userSkills = await profileHandler.getUserSkills(userId);

      // Group skills by category
      const groupedSkills = {};
      const categorySet = new Set();

      userSkills.forEach(skill => {
        const categoryName = skill.category_name || "Other";
        categorySet.add(categoryName);

        if (!groupedSkills[categoryName]) {
          groupedSkills[categoryName] = [];
        }
        groupedSkills[categoryName].push(skill.skill_name);
      });

      setSkills(groupedSkills);
      setCategories(Array.from(categorySet));
    } catch (err) {
      console.error("Failed to fetch skills:", err);
      // Set default empty state
      setSkills({});
      setCategories([]);
    } finally {
      setLoading(false);
    }
  };

  const handleAddCategory = () => {
    if (!newCategory.trim()) return;
    if (!categories.includes(newCategory)) {
      setCategories([...categories, newCategory]);
      setSkills({ ...skills, [newCategory]: [] });
    }
    setNewCategory("");
  };

  const handleAddSkill = () => {
    if (!selectedCategory || !newSkill.trim()) return;
    setSkills({
      ...skills,
      [selectedCategory]: [...(skills[selectedCategory] || []), newSkill],
    });
    setNewSkill("");
  };

  return (
    <section className="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm p-6 shadow-lg"
      style={{ backdropFilter: 'blur(20px) saturate(180%)', WebkitBackdropFilter: 'blur(20px) saturate(180%)' }}>
      {/* Header */}
      <div className="flex justify-between items-center mb-4">
        <h2 className="text-lg font-semibold text-white">💻 Skills</h2>
        <button
          onClick={() => setShowForm(!showForm)}
          className="text-sm px-3 py-1.5 rounded-lg bg-accent text-white hover:bg-accent/90 transition"
        >
          {showForm ? "Close" : "+ Add"}
        </button>
      </div>

      {showForm && (
        <div className="bg-white/10 rounded-lg p-4 mb-5 space-y-3 border border-white/10">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <select
              value={selectedCategory}
              onChange={(e) => setSelectedCategory(e.target.value)}
              className="w-full rounded-lg bg-transparent border border-white/20 text-white px-3 py-2 text-sm focus:ring-2 focus:ring-accent focus:outline-none"
            >
              <option value="">Select category</option>
              {categories.map((cat) => (
                <option key={cat} value={cat} className="text-black">
                  {cat}
                </option>
              ))}
            </select>
            <input
              type="text"
              value={newSkill}
              onChange={(e) => setNewSkill(e.target.value)}
              placeholder="Add new skill"
              className="w-full rounded-lg bg-transparent border border-white/20 text-white px-3 py-2 text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-accent focus:outline-none"
            />
          </div>

          <input
            type="text"
            value={newCategory}
            onChange={(e) => setNewCategory(e.target.value)}
            placeholder="Or create new category"
            className="w-full rounded-lg bg-transparent border border-white/20 text-white px-3 py-2 text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-accent focus:outline-none"
          />

          <div className="flex gap-2 justify-end">
            <button
              onClick={handleAddCategory}
              className="px-4 py-1.5 text-sm bg-white/10 text-white rounded-lg border border-white/20 hover:bg-white/20 transition"
            >
              Add Category
            </button>
            <button
              onClick={handleAddSkill}
              className="px-4 py-1.5 text-sm bg-accent text-white rounded-lg hover:bg-accent/90 transition"
            >
              Add Skill
            </button>
          </div>
        </div>
      )}

      {loading ? (
        <div className="text-center py-8">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-accent"></div>
          <p className="mt-2 text-sm text-muted">Loading skills...</p>
        </div>
      ) : categories.length === 0 ? (
        <div className="text-center py-8">
          <p className="text-muted">No skills added yet. Add your first skill!</p>
        </div>
      ) : (
        <div className="space-y-5">
          {categories.map((category) => (
            <div key={category}>
              <h3 className="text-base font-semibold text-accent mb-2">
                {category}
              </h3>
              <div className="flex flex-wrap gap-2">
                {skills[category]?.map((skill, index) => (
                  <span
                    key={`${skill}-${index}`}
                    className="px-2.5 py-1 text-sm rounded-md bg-white/10 border border-white/10 text-gray-200 hover:bg-white/20 transition"
                  >
                    {skill}
                  </span>
                ))}
              </div>
            </div>
          ))}
        </div>
      )}
    </section>
  );
}
