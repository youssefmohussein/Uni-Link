import { useState, useEffect } from "react";
import * as studentHandler from "../../../api/studentHandler";

export default function SkillsSection({ userId }) {
  const [skills, setSkills] = useState({});
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [newCategory, setNewCategory] = useState("");
  const [newSkill, setNewSkill] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("");
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    if (userId) {
      fetchUserSkills();
    }
  }, [userId]);

  const fetchUserSkills = async () => {
    try {
      setLoading(true);
      const userSkills = await studentHandler.getStudentSkills(userId);

      // Group skills by category
      const groupedSkills = {};
      const categorySet = new Set();

      userSkills.forEach(skill => {
        const categoryName = skill.category_name || "Other";
        categorySet.add(categoryName);

        if (!groupedSkills[categoryName]) {
          groupedSkills[categoryName] = [];
        }
        groupedSkills[categoryName].push({
          name: skill.skill_name,
          id: skill.skill_id
        });
      });

      setSkills(groupedSkills);
      setCategories(Array.from(categorySet));
    } catch (err) {
      console.error("Failed to fetch skills:", err);
      setSkills({});
      setCategories([]);
    } finally {
      setLoading(false);
    }
  };

  const handleAddCategory = () => {
    if (!newCategory.trim()) {
      alert("Please enter a category name");
      return;
    }
    const trimmedCategory = newCategory.trim();
    if (!categories.includes(trimmedCategory)) {
      setCategories([...categories, trimmedCategory]);
      setSkills({ ...skills, [trimmedCategory]: [] });
      setSelectedCategory(trimmedCategory);
    }
    setNewCategory("");
  };

  const handleAddSkill = async () => {
    if (!selectedCategory || !newSkill.trim()) {
      alert("Please select a category and enter a skill name");
      return;
    }

    try {
      setSaving(true);

      // Step 1: Add/Get category using studentHandler
      const category_id = await studentHandler.addSkillCategory(userId, selectedCategory);

      // Step 2: Add skill to skills table using studentHandler
      const skill_id = await studentHandler.addSkill(newSkill.trim(), category_id);

      // Step 3: Link skill to user using studentHandler
      await studentHandler.addStudentSkills(userId, [{ skill_id: skill_id }]);

      setNewSkill("");

      // Refresh skills
      await fetchUserSkills();
    } catch (err) {
      console.error("Failed to add skill:", err);
      alert(err.message || "Failed to add skill. Please try again.");
    } finally {
      setSaving(false);
    }
  };

  const handleRemoveSkill = async (categoryName, skillId, skillName) => {
    if (!confirm(`Remove "${skillName}" from your skills?`)) {
      return;
    }

    try {
      // Use studentHandler to remove skill
      await studentHandler.removeStudentSkill(userId, skillId);

      // Update local state
      const updatedCategorySkills = skills[categoryName].filter(s => s.id !== skillId);
      const updatedSkills = { ...skills };

      if (updatedCategorySkills.length === 0) {
        delete updatedSkills[categoryName];
        setCategories(categories.filter(c => c !== categoryName));
      } else {
        updatedSkills[categoryName] = updatedCategorySkills;
      }

      setSkills(updatedSkills);
    } catch (err) {
      console.error("Failed to remove skill:", err);
      alert(err.message || "Failed to remove skill. Please try again.");
    }
  };

  return (
    <section className="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm p-6 shadow-lg"
      style={{ backdropFilter: 'blur(20px) saturate(180%)', WebkitBackdropFilter: 'blur(20px) saturate(180%)' }}>
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
              onKeyPress={(e) => e.key === 'Enter' && !saving && handleAddSkill()}
              placeholder="Add new skill"
              className="w-full rounded-lg bg-transparent border border-white/20 text-white px-3 py-2 text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-accent focus:outline-none"
            />
          </div>

          <input
            type="text"
            value={newCategory}
            onChange={(e) => setNewCategory(e.target.value)}
            onKeyPress={(e) => e.key === 'Enter' && handleAddCategory()}
            placeholder="Or create new category"
            className="w-full rounded-lg bg-transparent border border-white/20 text-white px-3 py-2 text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-accent focus:outline-none"
          />

          <div className="flex gap-2 justify-end">
            <button
              onClick={handleAddCategory}
              disabled={!newCategory.trim()}
              className="px-4 py-1.5 text-sm bg-white/10 text-white rounded-lg border border-white/20 hover:bg-white/20 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Add Category
            </button>
            <button
              onClick={handleAddSkill}
              disabled={saving || !selectedCategory || !newSkill.trim()}
              className="px-4 py-1.5 text-sm bg-accent text-white rounded-lg hover:bg-accent/90 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {saving ? "Adding..." : "Add Skill"}
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
                    key={`${skill.id || skill.name}-${index}`}
                    className="group px-2.5 py-1 text-sm rounded-md bg-white/10 border border-white/10 text-gray-200 hover:bg-white/20 transition flex items-center gap-1"
                  >
                    {typeof skill === 'string' ? skill : skill.name}
                    <button
                      onClick={() => handleRemoveSkill(category, skill.id, skill.name)}
                      className="ml-1 text-red-400 hover:text-red-300 opacity-0 group-hover:opacity-100 transition-opacity"
                      title="Remove skill"
                    >
                      ×
                    </button>
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
