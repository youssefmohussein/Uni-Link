import React, { useState } from "react";
function QASection() {
  const [searchTerm, setSearchTerm] = useState("");
  const [filterCategory, setFilterCategory] = useState("all");
  const [questions, setQuestions] = useState([
    {
      id: 1,
      student: "Ahmed Mohamed",
      title: "React State Management Best Practices",
      question: "What's the best way to manage complex state in a large React application? Should I use Redux, Context API, or Zustand?",
      category: "Frontend",
      status: "unanswered",
      timestamp: "2024-01-20 14:30",
      answer: "",
      tags: ["React", "State Management", "Redux"]
    },
    {
      id: 2,
      student: "Fatima Ali",
      title: "Database Design for E-commerce",
      question: "I'm designing a database for an e-commerce platform. What tables should I include and how should I handle the relationships?",
      category: "Backend",
      status: "answered",
      timestamp: "2024-01-19 10:15",
      answer: "Great question! For an e-commerce database, you'll need tables for users, products, categories, orders, order_items, payments, and addresses. I'll send you a detailed ERD diagram.",
      tags: ["Database", "E-commerce", "MySQL"]
    },
    {
      id: 3,
      student: "Omar Hassan",
      title: "API Rate Limiting Implementation",
      question: "How can I implement rate limiting in my Node.js API to prevent abuse? What are the best practices?",
      category: "Backend",
      status: "unanswered",
      timestamp: "2024-01-18 16:45",
      answer: "",
      tags: ["Node.js", "API", "Security"]
    },
    {
      id: 4,
      student: "Mariam Ahmed",
      title: "CSS Grid vs Flexbox",
      question: "When should I use CSS Grid versus Flexbox for layout? Can you provide some practical examples?",
      category: "Frontend",
      status: "answered",
      timestamp: "2024-01-17 09:20",
      answer: "Use Flexbox for one-dimensional layouts (rows OR columns) and Grid for two-dimensional layouts (rows AND columns). Grid is perfect for complex page layouts, while Flexbox excels at component-level layouts.",
      tags: ["CSS", "Layout", "Grid", "Flexbox"]
    }
  ]);

  const [selectedQuestion, setSelectedQuestion] = useState(null);
  const [answerModal, setAnswerModal] = useState(false);

  const categories = ["all", "Frontend", "Backend", "Database", "DevOps", "General"];

  const filteredQuestions = questions.filter(question => {
    const matchesSearch = question.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         question.question.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         question.student.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         question.tags.some(tag => tag.toLowerCase().includes(searchTerm.toLowerCase()));
    const matchesCategory = filterCategory === "all" || question.category === filterCategory;
    return matchesSearch && matchesCategory;
  });

  const handleAnswer = (questionId, answer) => {
    setQuestions(questions.map(q => 
      q.id === questionId 
        ? { ...q, answer, status: "answered" }
        : q
    ));
    setAnswerModal(false);
    setSelectedQuestion(null);
  };

  const getStatusColor = (status) => {
    switch (status) {
      case "answered": return "text-green-400 bg-green-400/20";
      case "unanswered": return "text-yellow-400 bg-yellow-400/20";
      default: return "text-muted bg-main/20";
    }
  };

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-xl font-semibold">💬 Questions & Answers</h2>
        <div className="flex gap-4">
          <input
            type="text"
            placeholder="Search questions..."
            className="px-4 py-2 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
          <select
            value={filterCategory}
            onChange={(e) => setFilterCategory(e.target.value)}
            className="px-4 py-2 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
          >
            {categories.map(category => (
              <option key={category} value={category}>
                {category === "all" ? "All Categories" : category}
              </option>
            ))}
          </select>
        </div>
      </div>


      <div className="space-y-4">
        {filteredQuestions.map((question) => (
          <div key={question.id} className="border border-muted/30 rounded-custom p-4 hover:border-accent/50 transition">
            <div className="flex justify-between items-start mb-3">
              <div>
                <h3 className="font-semibold text-main">{question.title}</h3>
                <p className="text-sm text-muted">by {question.student}</p>
              </div>
              <div className="flex items-center gap-3">
                <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(question.status)}`}>
                  {question.status.charAt(0).toUpperCase() + question.status.slice(1)}
                </span>
                {question.status === "unanswered" && (
                  <button
                    onClick={() => {
                      setSelectedQuestion(question);
                      setAnswerModal(true);
                    }}
                    className="bg-accent text-white px-3 py-1 rounded-custom hover:opacity-80 text-sm"
                  >
                    Answer
                  </button>
                )}
              </div>
            </div>
            
            <p className="text-muted mb-3">{question.question}</p>
            
            <div className="flex flex-wrap gap-2 mb-3">
              {question.tags.map((tag, index) => (
                <span key={index} className="bg-accent/20 text-accent px-2 py-1 rounded text-xs">
                  {tag}
                </span>
              ))}
              <span className="bg-main/20 text-muted px-2 py-1 rounded text-xs">
                {question.category}
              </span>
            </div>
            
            {question.answer && (
              <div className="mt-4 p-3 bg-main/10 rounded-custom border-l-4 border-accent">
                <p className="text-sm font-medium text-accent mb-2">Your Answer:</p>
                <p className="text-muted text-sm">{question.answer}</p>
              </div>
            )}
            
            <div className="text-xs text-muted mt-3">
              Asked: {question.timestamp}
            </div>
          </div>
        ))}
      </div>

   
      {answerModal && selectedQuestion && (
        <AnswerModal
          question={selectedQuestion}
          onClose={() => setAnswerModal(false)}
          onSubmit={handleAnswer}
        />
      )}
    </div>
  );
}

function AnswerModal({ question, onClose, onSubmit }) {
  const [answer, setAnswer] = useState("");

  const handleSubmit = (e) => {
    e.preventDefault();
    if (answer.trim()) {
      onSubmit(question.id, answer);
    }
  };

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div className="bg-panel p-8 rounded-2xl shadow-2xl w-full max-w-2xl">
        <h3 className="text-xl font-semibold mb-4">Answer Question</h3>
        
        <div className="mb-6">
          <h4 className="font-medium text-main mb-2">{question.title}</h4>
          <p className="text-sm text-muted mb-4">by {question.student}</p>
          <p className="text-muted mb-4">{question.question}</p>
          
          <div className="flex flex-wrap gap-2">
            {question.tags.map((tag, index) => (
              <span key={index} className="bg-accent/20 text-accent px-2 py-1 rounded text-xs">
                {tag}
              </span>
            ))}
          </div>
        </div>

        <form onSubmit={handleSubmit}>
          <div className="mb-6">
            <label className="block text-sm font-medium mb-2">Your Answer</label>
            <textarea
              value={answer}
              onChange={(e) => setAnswer(e.target.value)}
              placeholder="Provide a detailed answer..."
              className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
              rows="6"
              required
            />
          </div>

          <div className="flex justify-end gap-3">
            <button
              type="button"
              onClick={onClose}
              className="px-5 py-2 rounded-custom bg-muted/20 text-muted hover:bg-muted/30 transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              className="px-5 py-2 rounded-custom bg-accent text-white hover:bg-accent/90 transition"
            >
              Submit Answer
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default QASection;






