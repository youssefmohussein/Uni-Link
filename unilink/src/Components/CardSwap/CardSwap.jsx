import React, { Children, cloneElement, forwardRef, isValidElement } from 'react';

export const Card = forwardRef(({ customClass, ...rest }, ref) => (
    <div
        ref={ref}
        {...rest}
        className={`absolute top-1/2 left-1/2 rounded-xl border border-white/20 bg-black shadow-xl ${customClass ?? ''} ${rest.className ?? ''}`.trim()}
        style={{ transform: 'translate(-50%, -50%)', ...rest.style }}
    />
));
Card.displayName = 'Card';

const CardSwap = ({
    width = 500,
    height = 400,
    cardDistance = 20,
    verticalDistance = 20,
    children
}) => {
    const childArr = Children.toArray(children);

    const rendered = childArr.map((child, i) => {
        // Calculate a static offset for each card in the stack
        const offsetX = i * cardDistance;
        const offsetY = -i * verticalDistance;
        const zIndex = childArr.length - i;
        
        return isValidElement(child)
            ? cloneElement(child, {
                key: i,
                style: { 
                    width, 
                    height, 
                    transform: `translate(calc(-50% + ${offsetX}px), calc(-50% + ${offsetY}px))`,
                    zIndex,
                    ...(child.props.style ?? {}) 
                }
            })
            : child;
    });

    return (
        <div
            className="absolute bottom-0 lg:right-0 left-1/2 lg:left-auto transform -translate-x-1/2 lg:translate-x-[5%] translate-y-[20%] origin-bottom lg:origin-bottom-right overflow-visible max-[768px]:translate-y-[25%] max-[768px]:scale-[0.75] max-[480px]:translate-y-[25%] max-[480px]:scale-[0.55]"
            style={{ width, height }}
        >
            {rendered}
        </div>
    );
};

export default CardSwap;
