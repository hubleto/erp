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

  renderFooter(): JSX.Element {
    return <>
      <div className="pr-4">
        {this.renderPrevRecordButton()}
        {this.renderNextRecordButton()}
      </div>
    </>;
  }

}
