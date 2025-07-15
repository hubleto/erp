import React, { Component } from "react";
import Form, { FormDescription, FormProps, FormState } from "adios/Form";

export interface HubletoFormProps extends FormProps {}
export interface HubletoFormState extends FormState {}

export default class HubletoForm<P, S> extends Form<HubletoFormProps,HubletoFormState> {
  static defaultProps: any = {
    ...Form.defaultProps
  };

  props: HubletoFormProps;
  state: HubletoFormState;

  constructor(props: HubletoFormProps) {
    super(props);

    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormProps) {
    return {
      ...super.getStateFromProps(props),
      isInlineEditing: true,
    }
  }

  renderCustomInputs(): Array<JSX.Element> {
    let customInputs: any = [];

    if (this.state?.description?.inputs) {
      Object.keys(this.state.description.inputs).map((inputName) => {
        const inputDesc = this.state.description.inputs[inputName];
        if (inputDesc.isCustom) {
          customInputs.push(this.inputWrapper(inputName));
        }
      });
    }

    return customInputs;
  }

  renderFooter(): null|JSX.Element {
    return <>
      <div className="pr-4">
        {this.renderPrevRecordButton()}
        {this.renderNextRecordButton()}
      </div>
    </>;
  }

  renderTopMenu(): null|JSX.Element {
    const topMenu = super.renderTopMenu();
    const dynamicMenu = globalThis.main.injectDynamicContent(
      this.constructor.name + ':TopMenu',
      {form: this}
    );

    if (dynamicMenu) {
      return <div className="flex gap-2 flex-col md:flex-row">
        <div className="flex flex-row gap-2 p-2">{topMenu}</div>
        <div className="flex flex-row gap-2 p-2">{dynamicMenu}</div>
      </div>;
    } else {
      return topMenu;
    }
  }

}
