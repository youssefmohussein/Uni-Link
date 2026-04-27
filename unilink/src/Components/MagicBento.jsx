import { FaChartLine, FaGripHorizontal, FaUsers, FaCog, FaPlug, FaShieldAlt } from 'react-icons/fa';

const cardData = [
    {
        title: 'Analytics',
        description: 'Track user behavior and performance metrics',
        label: 'Insights',
        icon: FaChartLine,
        iconColor: '#58a6ff'
    },
    {
        title: 'Dashboard',
        description: 'Centralized data view at a glance',
        label: 'Overview',
        icon: FaGripHorizontal,
        iconColor: '#58a6ff'
    },
    {
        title: 'Collaboration',
        description: 'Work together seamlessly in real-time',
        label: 'Teamwork',
        icon: FaUsers,
        iconColor: '#58a6ff'
    },
    {
        title: 'Automation',
        description: 'Streamline workflows with smart tools',
        label: 'Efficiency',
        icon: FaCog,
        iconColor: '#58a6ff'
    },
    {
        title: 'Integration',
        description: 'Connect your favorite tools effortlessly',
        label: 'Connectivity',
        icon: FaPlug,
        iconColor: '#58a6ff'
    },
    {
        title: 'Security',
        description: 'Enterprise-grade protection for your data',
        label: 'Protection',
        icon: FaShieldAlt,
        iconColor: '#58a6ff'
    }
];

const MagicBento = () => {
    return (
        <>
            <style>{`
                .bento-grid {
                    display: grid;
                    gap: 1rem;
                    width: 100%;
                    max-width: 75rem;
                    padding: 1.5rem;
                }
                .bento-card-grid {
                    display: grid;
                    gap: 1rem;
                    grid-template-columns: 1fr;
                    width: 100%;
                }
                @media (min-width: 600px) {
                    .bento-card-grid {
                        grid-template-columns: repeat(2, 1fr);
                    }
                }
                @media (min-width: 1024px) {
                    .bento-card-grid {
                        grid-template-columns: repeat(4, 1fr);
                    }
                    .bento-card-grid .bento-card:nth-child(3) {
                        grid-column: span 2;
                        grid-row: span 2;
                    }
                    .bento-card-grid .bento-card:nth-child(4) {
                        grid-column: 1 / span 2;
                        grid-row: 2 / span 2;
                    }
                    .bento-card-grid .bento-card:nth-child(6) {
                        grid-column: 4;
                        grid-row: 3;
                    }
                }
                .bento-card {
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    position: relative;
                    min-height: 240px;
                    width: 100%;
                    padding: 1.5rem;
                    border-radius: 24px;
                    border: 1px solid rgba(255, 255, 255, 0.125);
                    background: rgba(0, 0, 0, 0.2);
                    color: white;
                    overflow: hidden;
                    font-weight: 300;
                }
                .bento-card:hover {
                    background: rgba(255, 255, 255, 0.04);
                    border-color: rgba(88, 166, 255, 0.25);
                }
                .bento-card__icon {
                    font-size: 3rem;
                    margin-bottom: 1rem;
                }
                @media (max-width: 599px) {
                    .bento-card-grid {
                        grid-template-columns: 1fr;
                        width: 95%;
                        margin: 0 auto;
                    }
                    .bento-card {
                        min-height: 200px;
                    }
                    .bento-card__icon {
                        font-size: 2.5rem;
                    }
                }
            `}</style>

            <div className="bento-grid">
                <div className="bento-card-grid">
                    {cardData.map((card, index) => {
                        const Icon = card.icon;
                        return (
                            <div key={index} className="bento-card">
                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', gap: '0.75rem', color: 'white' }}>
                                    <span style={{ fontSize: '0.875rem', opacity: 0.8 }}>{card.label}</span>
                                </div>
                                <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', color: 'white', textAlign: 'center', flex: 1 }}>
                                    <Icon className="bento-card__icon" style={{ color: card.iconColor }} />
                                    <h3 style={{ fontWeight: 600, fontSize: '1.25rem', margin: '0 0 0.5rem' }}>{card.title}</h3>
                                    <p style={{ fontSize: '0.875rem', lineHeight: 1.6, opacity: 0.75 }}>{card.description}</p>
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </>
    );
};

export default MagicBento;
