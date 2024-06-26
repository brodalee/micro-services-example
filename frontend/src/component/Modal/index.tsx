import './style.scss'

import React from "react";

type Props = {
    children: React.ReactNode
    title: string
    footer?: React.ReactNode
    onClose?: () => void
    show: boolean
    size?: ModalSize
}

export enum ModalSize {
    BIG = 400,
    MEDIUM = 200,
    DEFAULT = ''
}

export default ({children, title, footer = null, show, onClose = () => {}, size = ModalSize.DEFAULT}: Props) => {
    if (!show) return null

    const close = () => {
        if (onClose) {
            onClose()
        }
    }

    return (
        <div className={'modal'}>
            <div className={`modal-content width-${size}`}>
                <div className={"modal-header"}>
                    <h4 className={"modal-title"}>{title}</h4>
                    <span onClick={close} className={"modal-close"} />
                </div>
                <div className={"modal-body"}>
                    {children}
                </div>
                {footer && (
                    <div className={"modal-footer"}>{footer}</div>
                )}
            </div>
        </div>
    )
}